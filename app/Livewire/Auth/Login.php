<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends Component
{
    // Kita gunakan variabel $login_id agar lebih umum (bisa username/NIP)
    public $login_id = ''; 
    public $password = '';
    public $remember = false;
    public $periode = '2025-2029';

    protected $rules = [
        'login_id' => 'required|string',
        'password' => 'required',
        'periode'  => 'required',
    ];

    protected $messages = [
        'login_id.required' => 'Username atau NIP wajib diisi.',
        'password.required' => 'Password wajib diisi.',
    ];

    public function login()
    {
        $this->validate();

        $throttleKey = Str::lower($this->login_id) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'login_id' => "Terlalu banyak percobaan. Tunggu $seconds detik.",
            ]);
        }

        // --- LOGIKA BARU: DETEKSI NIP ATAU USERNAME ---
        
        // Cek apakah input hanya angka? Jika ya, asumsikan NIP.
        // Jika ada huruf, asumsikan Username.
        $loginType = is_numeric($this->login_id) ? 'nip' : 'username';

        $credentials = [
            $loginType => $this->login_id,
            'password' => $this->password
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            
            RateLimiter::clear($throttleKey);
            session()->regenerate();
            session(['periode_renstra' => $this->periode]);

            $user = Auth::user();

            // Validasi tambahan: Admin harus login pakai Username, Pegawai/Pimpinan pakai NIP
            // (Opsional: Hapus blok if ini jika ingin Admin juga boleh login pakai NIP jika punya)
            if ($user->role === 'admin' && $loginType === 'nip') {
                Auth::logout();
                $this->addError('login_id', 'Admin harus login menggunakan Username.');
                return;
            }

            return match ($user->role) {
                'admin'     => redirect()->route('admin.dashboard'),
                'pimpinan'  => redirect()->route('pimpinan.dashboard'),
                default     => redirect()->route('dashboard'),
            };
        }

        RateLimiter::hit($throttleKey, 60);

        $this->addError('login_id', 'Kombinasi akun dan password salah.');
        $this->password = ''; 
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest');
    }
}