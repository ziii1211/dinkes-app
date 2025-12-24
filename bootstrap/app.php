<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // 1. DAFTARKAN ALIAS (Untuk dipakai spesifik di route)
        // Contoh pemakaian di route: middleware('role:admin')
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // 2. DAFTARKAN MIDDLEWARE WEB (GLOBAL)
        // SecurityHeaders akan otomatis berjalan di SEMUA halaman tanpa dipanggil
        // Ini melindungi seluruh aplikasi dari serangan Clickjacking & XSS
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();