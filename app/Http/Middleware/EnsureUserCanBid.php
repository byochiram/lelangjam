<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanBid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Harus login (walau biasanya sudah ada 'auth' juga)
        if (! $user) {
            return redirect()->route('login');
        }

        // 2. Harus BIDDER
        if (! $user->isBidder()) {
            return redirect()
                ->route('home')
                ->with('error', 'Hanya akun bidder yang dapat melakukan bid.');
        }

        // 3. Tidak boleh SUSPENDED
        if ($user->isSuspended()) {
            $msg = 'Akun Anda sedang ditangguhkan. Anda tidak dapat melakukan bid.';

            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'message' => $msg,
                ], 403);
            }

            return redirect()
                ->route('home')
                ->with('error', $msg);
        }

        // 4. Email wajib terverifikasi
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        
        return $next($request);
    }
}
