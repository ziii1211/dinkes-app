<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter; // Wajib import ini
use Illuminate\Support\Str;                 // Wajib import ini
use Illuminate\Validation\ValidationException; // Wajib import ini

class Login extends Component
{
    public $username = '';
    public $password = '';
    public $remember = false;
    
    // Default value periode (sesuai file asli Anda)
    public $periode = '2025-2029';

    protected $rules = [
        'username' => 'required|string',
        'password' => 'required',
        'periode'  => 'required',
    ];

    protected $messages = [
        'username.required' => 'Username wajib diisi.',
        'password.required' => 'Password wajib diisi.',
    ];

    public function login()
    {
        $this->validate();

        // 1. MEMBUAT KUNCI PEMBATAS (THROTTLE KEY)
        // Kunci unik gabungan Username (kecil) + IP Address
        $throttleKey = Str::lower($this->username) . '|' . request()->ip();

        // 2. CEK APAKAH SEDANG DIBLOKIR? (Maksimal 5x salah)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            // Lempar error jika terlalu banyak percobaan
            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login. Silakan tunggu $seconds detik lagi.",
            ]);
        }

        // 3. COBA LOGIN
        if (Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            
            // JIKA SUKSES:
            // Hapus catatan percobaan gagal (Reset Counter)
            RateLimiter::clear($throttleKey);
            
            session()->regenerate();
            
            // Simpan periode ke session (Sesuai logika aplikasi Anda)
            session(['periode_renstra' => $this->periode]);

            // Redirect sesuai Role
            // Pastikan user punya kolom 'role' di database
            $role = Auth::user()->role ?? 'pegawai'; // Default ke pegawai jika null

            return match ($role) {
                'admin' => redirect()->route('admin.dashboard'),
                'pimpinan' => redirect()->route('pimpinan.dashboard'),
                default => redirect()->route('dashboard'),
            };
        }

        // 4. JIKA GAGAL:
        // Hitung +1 kegagalan (Blokir selama 60 detik jika sudah 5x)
        RateLimiter::hit($throttleKey, 60);

        $this->addError('username', 'Username atau password salah.');
        $this->password = ''; // Kosongkan password agar user mengetik ulang
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest');
    }
}