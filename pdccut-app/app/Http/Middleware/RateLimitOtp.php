<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitOtp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $nationalCode = $request->input('national_code');
        $ip = $request->ip();
        
        // Rate limit per national code (max 3 attempts per 15 minutes)
        $nationalCodeKey = 'otp_national_code_' . $nationalCode;
        if (RateLimiter::tooManyAttempts($nationalCodeKey, 3)) {
            $seconds = RateLimiter::availableIn($nationalCodeKey);
            return back()->withErrors([
                'national_code' => "تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً {$seconds} ثانیه دیگر تلاش کنید."
            ]);
        }
        
        // Rate limit per IP (max 10 attempts per 15 minutes)
        $ipKey = 'otp_ip_' . $ip;
        if (RateLimiter::tooManyAttempts($ipKey, 10)) {
            $seconds = RateLimiter::availableIn($ipKey);
            return back()->withErrors([
                'national_code' => "تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً {$seconds} ثانیه دیگر تلاش کنید."
            ]);
        }
        
        // Increment counters
        RateLimiter::hit($nationalCodeKey, 900); // 15 minutes
        RateLimiter::hit($ipKey, 900); // 15 minutes
        
        return $next($request);
    }
}
