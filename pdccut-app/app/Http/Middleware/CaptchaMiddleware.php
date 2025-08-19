<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class CaptchaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if CAPTCHA should be verified
        $failedAttempts = Session::get('failed_attempts', 0);
        $hasQuestion = Session::has('captcha_question');
        
        if ($failedAttempts >= 2 || $hasQuestion) {
            // Verify CAPTCHA
            $captchaAnswer = $request->input('captcha_answer');
            $sessionCaptcha = Session::get('captcha_question');
            
            if (!$captchaAnswer || !$sessionCaptcha || (int) $captchaAnswer !== (int) ($sessionCaptcha['answer'] ?? null)) {
                return back()->withErrors([
                    'captcha_answer' => 'کد امنیتی وارد شده صحیح نمی‌باشد.'
                ])->withInput();
            }
        }
        
        return $next($request);
    }
}
