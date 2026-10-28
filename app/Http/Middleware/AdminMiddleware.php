<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Memeriksa apakah user yang mengakses berstatus login dan memiliki role Admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('home')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman admin.')
                ->with('auth_modal', true)
                ->with('auth_form', 'login');
        }

        // 2. Cek apakah role user adalah admin
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses Administrator.');
        }

        return $next($request);
    }
}
