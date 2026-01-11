<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Login extends Component
{
    // Variabel ini HARUS SAMA dengan yang ada di login.blade.php
    public $username = '';
    public $password = '';
    public $remember = false;      // <--- Penting! Biar gak error "Property not found"
    public $periode = '2025-2029';

    public function login()
    {
        // 1. Validasi Input (Biar gak kosong)
        $this->validate([
            'username' => 'required', 
            'password' => 'required'
        ]);

        // 2. CARI USER MANUAL (Bypass Auth::attempt untuk Debug)
        // Kita cari user yang username-nya cocok
        $user = User::where('username', $this->username)->first();

        // 3. Cek Password Manual
        // Kalau user ketemu DAN passwordnya cocok (sudah di-hash)
        if ($user && Hash::check($this->password, $user->password)) {
            
            // 4. PAKSA LOGIN (Login Using ID lebih 'bandel' daripada attempt biasa)
            Auth::loginUsingId($user->id, $this->remember);
            
            // 5. Simpan periode & Regenerasi Session
            session(['periode_renstra' => $this->periode]);
            session()->regenerate();

            // 6. Redirect Sesuai Role (Langsung tembak URL)
            if ($user->role === 'admin') {
                return redirect()->to('/admin/dashboard');
            } elseif ($user->role === 'pimpinan') {
                return redirect()->to('/pimpinan/dashboard');
            } else {
                return redirect()->to('/');
            }

        } else {
            // Jika Gagal (Password salah atau User tidak ada)
            $this->addError('username', 'Login Gagal. Cek Username & Password.');
        }
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest');
    }
}