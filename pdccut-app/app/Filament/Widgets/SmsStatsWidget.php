<?php

namespace App\Filament\Widgets;

use App\Models\SmsLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SmsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSms = SmsLog::count();
        $todaySms = SmsLog::whereDate('created_at', today())->count();
        $failedSms = SmsLog::where('status', 'failed')->count();
        $deliveredSms = SmsLog::where('status', 'delivered')->count();
        $pendingSms = SmsLog::where('status', 'pending')->count();
        $sentSms = SmsLog::where('status', 'sent')->count();

        return [
            Stat::make('کل پیامک‌ها', $totalSms)
                ->description('مجموع پیامک‌های ارسالی')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),

            Stat::make('پیامک‌های امروز', $todaySms)
                ->description('پیامک‌های ارسالی امروز')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('پیامک‌های ناموفق', $failedSms)
                ->description('پیامک‌هایی که ارسال نشده‌اند')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('پیامک‌های تحویل شده', $deliveredSms)
                ->description('پیامک‌هایی که تحویل شده‌اند')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('پیامک‌های در انتظار', $pendingSms)
                ->description('پیامک‌هایی که در صف ارسال هستند')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('پیامک‌های ارسال شده', $sentSms)
                ->description('پیامک‌هایی که ارسال شده‌اند')
                ->color('info'),
        ];
    }
}
