<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AccountLockoutMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $key = 'lockout_' . $ip;

        if (Cache::get($key, 0) >= 5) {
            return response('Too many attempts. Try later.', 429);
        }

        try {
            return $next($request);
        } catch (\Throwable $e) {
            Cache::increment($key);
            Cache::put($key, Cache::get($key, 0), now()->addMinutes(15));
            // Avoid logging sensitive request data
            \Log::warning('Auth attempt failed', [
                'ip' => $ip,
                'path' => $request->path(),
            ]);
            throw $e;
        }
    }
}
