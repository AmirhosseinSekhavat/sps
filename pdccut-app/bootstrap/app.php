<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'rate.limit.otp' => \App\Http\Middleware\RateLimitOtp::class,
            'captcha' => \App\Http\Middleware\CaptchaMiddleware::class,
            'account.lockout' => \App\Http\Middleware\AccountLockoutMiddleware::class,
            'ip.blacklist' => \App\Http\Middleware\IpBlacklistMiddleware::class,
        ]);
        $middleware->trustProxies(at: '*');
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
