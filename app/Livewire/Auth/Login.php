<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    // Ubah variabel public dari email ke username
    public $username = '';
    public $password = '';
    public $remember = false;

    // Aturan validasi diubah
    protected $rules = [
        'username' => 'required|string',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        // Auth::attempt menggunakan key 'username'
        if (Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            // LOGIKA REDIRECT
            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }
            
            if (Auth::user()->role === 'pimpinan') {
                return redirect()->intended(route('pimpinan.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        // Error message jika gagal
        $this->addError('username', 'Username atau password yang Anda masukkan salah.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest'); 
    }
}