<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Anti-Clickjacking: Mencegah website di-embed di iframe orang lain
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Anti-MIME Sniffing: Mencegah browser salah tebak jenis file
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. XSS Protection: Mengaktifkan filter XSS bawaan browser lama
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Strict Transport Security: Paksa browser ingat untuk selalu pakai HTTPS
        // (Hanya aktif jika request sudah HTTPS)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}