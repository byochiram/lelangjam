<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // belum login
        if (! $user) {
            return redirect()->route('login');
        }

        // bukan admin (ADMIN / SUPERADMIN)
        if (! $user->isAdmin()) {
            return redirect()->route('home')
                ->with('error', 'Anda tidak memiliki akses ke halaman admin.');
        }

        // ADMIN / SUPERADMIN tapi status SUSPENDED
        if ($user->isSuspended()) {
            // boleh pilih: logout atau tetap login.
            // ini contoh: tetap login tapi dianggap seperti user biasa
            return redirect()->route('home')
                ->with('error', 'Akun admin Anda sedang ditangguhkan. Silakan hubungi superadmin.');
        }

        return $next($request);
    }

}
