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

        // 5. Content Security Policy (CSP)
        // UPDATE: Menambahkan 'https://cdn.jsdelivr.net' untuk Flatpickr (Datepicker)
        // Tetap mempertahankan 'https://cdn.tailwindcss.com' dan Google Fonts
        
        $csp = "default-src 'self'; " .
               // Script: allow Livewire (eval/inline), Tailwind CDN, dan JSDelivr (Flatpickr)
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net; " .
               // Style: allow Inline styles, Google Fonts, dan JSDelivr (Flatpickr CSS)
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; " .
               // Font: allow Google Fonts data
               "font-src 'self' https://fonts.gstatic.com; " .
               // Img: allow Images from self, data URI (base64), dan HTTPS sources
               "img-src 'self' data: https:; " .
               "connect-src 'self'; " .
               "frame-src 'self'; " .
               "object-src 'none';";

        $response->headers->set('Content-Security-Policy', $csp);
        
        // Tambahan: Permissions Policy untuk keamanan ekstra
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}