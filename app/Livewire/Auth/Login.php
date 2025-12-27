<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Jabatan; // PENTING: Import Model Jabatan

class Login extends Component
{
    public $username = '';
    public $password = '';
    public $remember = false;
    public $periode = '2025-2029';

    protected $rules = [
        'username' => 'required|string',
        'password' => 'required',
        'periode'  => 'required',
    ];

    protected $messages = [
        'username.required' => 'Silakan pilih Jabatan Anda.',
        'password.required' => 'Password (NIP) wajib diisi.',
    ];

    public function login()
    {
        $this->validate();

        $throttleKey = Str::lower($this->username) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan. Tunggu $seconds detik.",
            ]);
        }

        // Login menggunakan Username (Nama Jabatan) & Password (NIP)
        if (Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            
            RateLimiter::clear($throttleKey);
            session()->regenerate();
            session(['periode_renstra' => $this->periode]);

            $user = Auth::user();

            return match ($user->role) {
                'admin'     => redirect()->route('admin.dashboard'),
                'pimpinan'  => redirect()->route('pimpinan.dashboard'),
                default     => redirect()->route('dashboard'),
            };
        }

        RateLimiter::hit($throttleKey, 60);

        $this->addError('username', 'Kombinasi Jabatan dan NIP tidak cocok.');
        $this->password = ''; 
    }

    public function render()
    {
        // PENTING: Ambil data jabatan dari database
        // Urutkan berdasarkan nama agar mudah dicari di dropdown
        $jabatans = Jabatan::orderBy('nama', 'asc')->get();

        return view('livewire.auth.login', [
            'jabatans' => $jabatans
        ])->layout('components.layouts.guest');
    }
}