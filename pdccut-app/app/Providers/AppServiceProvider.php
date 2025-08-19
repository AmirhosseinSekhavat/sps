<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share unread notifications count with the main app layout
        View::composer('layouts.app', function ($view) {
            if (Auth::check()) {
                $unreadCount = Auth::user()
                    ->notifications()
                    ->where('is_read', false)
                    ->count();
                $view->with('unreadNotificationsCount', $unreadCount);
            }
        });
    }
}
