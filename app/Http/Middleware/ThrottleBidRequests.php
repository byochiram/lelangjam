<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleBidRequests
{
    // misal: max 3 bid per detik per user/IP
    protected int $maxAttempts  = 3;
    protected int $decaySeconds = 1; // jendela waktu (1 detik)

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // key gabungan user id / IP + lot
        $userId = optional($request->user())->id ?: 'guest';
        $ip     = $request->ip();
        $lotId  = $request->route('lot')?->id ?? 'x';

        $key = sprintf('bid:%s:%s:%s', $userId, $ip, $lotId);

        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            // Kalau bid pakai AJAX → balas JSON
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'message' => "Terlalu banyak percobaan bid. Silakan coba lagi dalam {$seconds} detik.",
                ], 429);
            }

            // fallback non-AJAX
            return back()->with('error', "Terlalu banyak percobaan bid. Silakan coba lagi dalam {$seconds} detik.");
        }

        // hit: expire dalam X detik
        RateLimiter::hit($key, $this->decaySeconds);

        return $next($request);
    }
}
