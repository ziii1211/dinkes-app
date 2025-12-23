<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class Login extends Component
{
    // Properti form
    public $username = '';
    public $password = '';
    public $remember = false;
    
    // Default value untuk periode
    public $periode = '2025-2029';

    // Rules validasi
    protected $rules = [
        'username' => 'required|string',
        'password' => 'required',
        'periode'  => 'required',
    ];

    // Custom messages
    protected $messages = [
        'username.required' => 'Username wajib diisi.',
        'password.required' => 'Password wajib diisi.',
    ];

    public function login()
    {
        $this->validate();

        // 1. Kunci Rate Limiting (Gabungan Username + IP Address)
        // Ini memastikan pembatasan berlaku unik per user dan per lokasi
        $throttleKey = strtolower($this->username) . '|' . request()->ip();

        // 2. Cek apakah user sudah mencoba login terlalu sering (Max 5 kali)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login. Silakan tunggu $seconds detik lagi.",
            ]);
        }

        // 3. Coba Login ke Aplikasi
        if (Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            
            // Jika BERHASIL login:
            session()->regenerate();
            
            // Hapus catatan gagal login (reset counter ke 0)
            RateLimiter::clear($throttleKey);

            // Simpan periode ke session
            session(['periode_renstra' => $this->periode]);

            // Logika Redirect berdasarkan Role
            $role = Auth::user()->role;

            return match ($role) {
                'admin' => redirect()->intended(route('admin.dashboard')),
                'pimpinan' => redirect()->intended(route('pimpinan.dashboard')),
                default => redirect()->intended(route('dashboard')),
            };
        }

        // 4. Jika GAGAL login:
        // Catat 1 kali kegagalan ke dalam sistem
        RateLimiter::hit($throttleKey);

        $this->addError('username', 'Username atau password tidak sesuai.');
        $this->password = ''; // Reset password field agar user mengetik ulang
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest'); 
    }
}