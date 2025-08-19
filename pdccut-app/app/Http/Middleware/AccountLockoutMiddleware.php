<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AccountLockoutMiddleware
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
        
        // Check if account is locked
        $lockoutKey = 'account_lockout_' . $nationalCode;
        $ipLockoutKey = 'ip_lockout_' . $ip;
        
        // Check national code lockout (5 failed attempts = 30 minutes lockout)
        if (Cache::has($lockoutKey)) {
            $lockoutTime = Cache::get($lockoutKey);
            $remainingTime = $lockoutTime - time();
            
            if ($remainingTime > 0) {
                $minutes = ceil($remainingTime / 60);
                Log::warning("Locked account access attempt: {$nationalCode} from IP: {$ip}");
                
                return back()->withErrors([
                    'national_code' => "حساب کاربری شما به دلیل تلاش‌های ناموفق قفل شده است. لطفاً {$minutes} دقیقه دیگر تلاش کنید."
                ]);
            } else {
                // Lockout expired, remove it
                Cache::forget($lockoutKey);
            }
        }
        
        // Check IP lockout (10 failed attempts = 60 minutes lockout)
        if (Cache::has($ipLockoutKey)) {
            $lockoutTime = Cache::get($ipLockoutKey);
            $remainingTime = $lockoutTime - time();
            
            if ($remainingTime > 0) {
                $minutes = ceil($remainingTime / 60);
                Log::warning("Locked IP access attempt: {$ip} for national_code: {$nationalCode}");
                
                return back()->withErrors([
                    'national_code' => "دسترسی شما به دلیل تلاش‌های ناموفق قفل شده است. لطفاً {$minutes} دقیقه دیگر تلاش کنید."
                ]);
            } else {
                // Lockout expired, remove it
                Cache::forget($ipLockoutKey);
            }
        }
        
        return $next($request);
    }
}
