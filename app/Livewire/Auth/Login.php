<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends Component
{
    public $login_id = ''; 
    public $password = '';
    public $remember = false;
    
    public $periode = '2025-2029';
    public $role = 'pegawai'; // Default

    protected $rules = [
        'login_id' => 'required|string',
        'password' => 'required',
        'periode'  => 'required',
        'role'     => 'required|in:admin,pegawai,pimpinan',
    ];

    protected $messages = [
        'login_id.required' => 'Username atau NIP wajib diisi.',
        'password.required' => 'Password wajib diisi.',
        'role.required'     => 'Silakan pilih peran (role) login.',
    ];

    public function login()
    {
        $this->validate();

        // 1. Rate Limiter
        $throttleKey = Str::lower($this->login_id) . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'login_id' => "Terlalu banyak percobaan. Tunggu $seconds detik.",
            ]);
        }

        // 2. Bersihkan Input (Trim Spasi) - PENTING!
        // Seringkali copy-paste NIP membawa spasi di awal/akhir
        $cleanLoginId = trim($this->login_id); 

        // 3. Tentukan Username Target Utama
        $primaryUsername = $cleanLoginId;

        if ($this->role === 'pimpinan') {
            // Target Utama: Harus ada .pimpinan
            if (!str_ends_with($cleanLoginId, '.pimpinan')) {
                $primaryUsername = $cleanLoginId . '.pimpinan';
            }
        } elseif ($this->role === 'pegawai') {
            // Target Utama: Harus NIP polos
            $primaryUsername = str_replace('.pimpinan', '', $cleanLoginId);
        }

        // 4. PROSES LOGIN (Smart Try)
        $loginSuccess = false;

        // Percobaan 1: Sesuai Format Baku
        if (Auth::attempt(['username' => $primaryUsername, 'password' => $this->password], $this->remember)) {
            $loginSuccess = true;
        } 
        // Percobaan 2: Fallback (Khusus Pimpinan)
        // Jika gagal login pakai nip.pimpinan, coba login pakai NIP polos saja.
        // Ini mengatasi akun Kepala Dinas yang mungkin dibuat sebelum sistem update.
        elseif ($this->role === 'pimpinan') {
            $fallbackUsername = str_replace('.pimpinan', '', $cleanLoginId);
            if (Auth::attempt(['username' => $fallbackUsername, 'password' => $this->password], $this->remember)) {
                $loginSuccess = true;
            }
        }

        // 5. Jika Berhasil Login
        if ($loginSuccess) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();
            session(['periode_renstra' => $this->periode]);

            $user = Auth::user();

            // Validasi Role (Security Check)
            // Pastikan User yang login role-nya BENAR-BENAR sesuai dropdown
            if ($user->role !== $this->role) {
                Auth::logout();
                $this->addError('role', 'Akun ditemukan, tetapi Role tidak sesuai. Pastikan Anda memilih Role yang benar (Admin/Pimpinan/Pegawai).');
                return;
            }

            // Redirect
            return match ($user->role) {
                'admin'     => redirect()->route('admin.dashboard'),
                'pimpinan'  => redirect()->route('pimpinan.dashboard'),
                default     => redirect()->route('dashboard'),
            };
        }

        // 6. Jika Gagal Semua Percobaan
        RateLimiter::hit($throttleKey, 60);
        $this->addError('login_id', 'Kombinasi NIP/Username dan Password salah.');
        $this->password = ''; 
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest');
    }
}