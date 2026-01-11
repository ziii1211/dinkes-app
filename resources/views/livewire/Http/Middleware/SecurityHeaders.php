<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Anti-Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Anti-MIME Sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. XSS Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Strict Transport Security (Hanya aktif jika HTTPS)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // 5. Content Security Policy (CSP) - PERBAIKAN
        // Kita tambahkan 'https://cdn.tailwindcss.com' agar tampilan tidak hancur
        
        $csp = "default-src 'self'; " .
               // PERBAIKAN DI SINI: Menambahkan cdn.tailwindcss.com
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "img-src 'self' data:;";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}