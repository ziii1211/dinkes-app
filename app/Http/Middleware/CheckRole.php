<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Menangani permintaan yang masuk.
     * Middleware ini akan memeriksa apakah user memiliki role yang sesuai.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Cek Role User menggunakan helper hasRole()
        // Menggunakan $request->user() lebih aman daripada Auth::user() dalam konteks middleware
        if (! $request->user()->hasRole($role)) {
            // Jika role tidak sesuai -> TOLAK dengan Error 403
            abort(403, 'AKSES DITOLAK: Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}