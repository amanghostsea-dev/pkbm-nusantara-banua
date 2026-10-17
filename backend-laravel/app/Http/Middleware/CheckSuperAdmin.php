<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    /**
     * Pastikan pengguna memiliki hak akses Administrator Utama (Super Admin)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Hanya Administrator Utama yang diizinkan
        if ($request->user()->role !== 'admin' || ! $request->user()->is_super_admin) {
            abort(403, 'Akses Terbatas: Fitur ini hanya dapat dikelola oleh Administrator Utama.');
        }

        return $next($request);
    }
}
