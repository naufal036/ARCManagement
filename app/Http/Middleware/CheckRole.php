<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('login');
        }

        // Ambil nama role user yang sedang login
        $userRole = Auth::user()->role->name ?? '';

        // Cek apakah role user ada di dalam daftar role yang diizinkan ($roles)
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Jika tidak sesuai, tolak akses dan arahkan ke dashboard
        abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
    }
}
