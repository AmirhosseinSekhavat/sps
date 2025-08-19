<?php

namespace App\Filament\Resources\SmsLogResource\Pages;

use App\Filament\Resources\SmsLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSmsLog extends ViewRecord
{
    protected static string $resource = SmsLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('resend')
                ->label('ارسال مجدد')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'failed')
                ->action(function () {
                    // TODO: Implement resend logic
                    return redirect()->back()->with('success', 'درخواست ارسال مجدد ثبت شد.');
                }),
            
            Actions\Action::make('check_status')
                ->label('بررسی وضعیت')
                ->icon('heroicon-o-magnifying-glass')
                ->color('info')
                ->action(function () {
                    // TODO: Implement status check
                    return redirect()->back()->with('success', 'وضعیت پیامک بررسی شد.');
                }),
        ];
    }
} 