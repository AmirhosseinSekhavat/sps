<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IpBlacklistMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        
        // Check if IP is blacklisted
        if (Cache::has('ip_blacklist_' . $ip)) {
            Log::warning("Blacklisted IP access attempt: {$ip}");
            
            return response()->json([
                'error' => 'دسترسی شما مسدود شده است.',
                'message' => 'IP شما در لیست سیاه قرار دارد.'
            ], 403);
        }
        
        // Check for suspicious activity patterns
        $suspiciousKey = 'suspicious_activity_' . $ip;
        $suspiciousCount = Cache::get($suspiciousKey, 0);
        
        // If more than 20 failed attempts in 1 hour, blacklist IP
        if ($suspiciousCount >= 20) {
            Cache::put('ip_blacklist_' . $ip, true, now()->addHours(24));
            Log::critical("IP blacklisted due to suspicious activity: {$ip}");
            
            return response()->json([
                'error' => 'دسترسی شما مسدود شده است.',
                'message' => 'IP شما به دلیل فعالیت مشکوک مسدود شده است.'
            ], 403);
        }
        
        return $next($request);
    }
}
