<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            // LOGIKA REDIRECT
            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }
            // TAMBAHAN UNTUK PIMPINAN
            if (Auth::user()->role === 'pimpinan') {
                return redirect()->intended(route('pimpinan.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        $this->addError('email', 'Email atau password yang Anda masukkan salah.');
    }

    public function render()
    {
        // Pastikan layout yang dipakai adalah layout khusus guest (login page)
        // Biasanya layoutnya berbeda dengan dashboard dalam (tanpa sidebar)
        return view('livewire.auth.login')->layout('components.layouts.guest'); 
        // Jika kamu tidak punya layout guest, ganti dengan ->layout('components.layouts.app') 
        // tapi nanti sidebar akan muncul di halaman login.
    }
}