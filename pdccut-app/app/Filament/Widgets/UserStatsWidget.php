<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\ShareCertificate;
use App\Models\Notification;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected ?string $heading = 'آمار کاربران';

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $usersWithCertificates = User::has('shareCertificates')->count();
        $totalNotifications = Notification::count();
        $unreadNotifications = Notification::where('is_read', false)->count();
        $adminUsers = User::where('is_admin', true)->count();

        return [
            Stat::make('کل کاربران', $totalUsers)
                ->description('تعداد کل کاربران ثبت شده')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),

            Stat::make('کاربران فعال', $activeUsers)
                ->description('کاربران فعال در سیستم')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('کاربران دارای گواهی', $usersWithCertificates)
                ->description('کاربرانی که گواهی سهام دارند')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('info'),

            Stat::make('مدیران سیستم', $adminUsers)
                ->description('تعداد ادمین‌ها')
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('warning'),

            Stat::make('کل اعلان‌ها', $totalNotifications)
                ->description('تعداد کل اعلان‌های ارسال شده')
                ->descriptionIcon('heroicon-o-bell')
                ->color('warning'),

            Stat::make('اعلان‌های نخوانده', $unreadNotifications)
                ->description('اعلان‌هایی که هنوز خوانده نشده‌اند')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
