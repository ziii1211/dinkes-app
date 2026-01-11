<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;      // <--- INI YANG SEBELUMNYA KURANG
use Illuminate\Support\Facades\URL;       // <--- Untuk Paksa HTTPS
use Illuminate\Validation\Rules\Password; // <--- Untuk Aturan Password Aman

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Pagination pakai Bootstrap (Agar tampilan tabel rapi)
        // Tanpa baris 'use' di atas, kode ini yang bikin error
        Paginator::useBootstrap();

        // 2. PAKSA HTTPS SAAT ONLINE (PRODUCTION)
        // Ini mencegah error "Mixed Content" dan serangan MITM saat di hosting
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // 3. ATURAN PASSWORD AMAN (SECURITY HARDENING)
        // Mencegah pegawai menggunakan password lemah (misal: "123456")
        Password::defaults(function () {
            return Password::min(8)
                ->letters()       // Wajib ada huruf
                ->numbers()       // Wajib ada angka
                ->mixedCase()     // Wajib huruf Besar & Kecil
                ->uncompromised(); // Cek database password bocor global
        });
    }
}