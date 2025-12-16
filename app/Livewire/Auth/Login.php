<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    // Properti form
    public $username = '';
    public $password = '';
    public $remember = false;
    
    // Default value untuk periode (PENTING: ini harus ada karena dipakai di view)
    public $periode = '2025-2029';

    // Rules validasi
    protected $rules = [
        'username' => 'required|string',
        'password' => 'required',
        'periode'  => 'required',
    ];

    // Custom messages agar lebih ramah
    protected $messages = [
        'username.required' => 'Username wajib diisi.',
        'password.required' => 'Password wajib diisi.',
    ];

    public function login()
    {
        $this->validate();

        // Coba login
        if (Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            // Simpan periode terpilih ke session jika diperlukan nanti
            session(['periode_renstra' => $this->periode]);

            // Logika Redirect berdasarkan Role
            $role = Auth::user()->role;

            return match ($role) {
                'admin' => redirect()->intended(route('admin.dashboard')),
                'pimpinan' => redirect()->intended(route('pimpinan.dashboard')),
                default => redirect()->intended(route('dashboard')),
            };
        }

        // Jika gagal
        $this->addError('username', 'Username atau password tidak sesuai.');
        $this->password = ''; // Reset password field
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest'); 
    }
}