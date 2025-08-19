<?php

namespace App\Filament\Resources\SmsLogResource\Pages;

use App\Filament\Resources\SmsLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\SmsLog;
use App\Exports\SmsLogsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\SmsService;

class ListSmsLogs extends ListRecords
{
	protected static string $resource = SmsLogResource::class;

	protected function getHeaderActions(): array
	{
		return [
			Actions\Action::make('export')
				->label('خروجی Excel')
				->icon('heroicon-o-arrow-down-tray')
				->color('success')
				->action(function () {
					// Export all logs (latest first). Filters can be added later when needed.
					$query = SmsLog::query()->orderByDesc('created_at');
					$filename = 'sms_logs_'.now()->format('Ymd_His').'.xlsx';
					return Excel::download(new SmsLogsExport($query), $filename);
				}),
			
			Actions\Action::make('refresh_status')
				->label('بروزرسانی وضعیت')
				->icon('heroicon-o-arrow-path')
				->color('info')
				->requiresConfirmation()
				->action(function () {
					$service = app(SmsService::class);
					$logs = SmsLog::query()
						->whereIn('status', ['pending', 'sent'])
						->whereNotNull('provider_response_id')
						->orderByDesc('created_at')
						->limit(200)
						->get();
					$checked = 0;
					foreach ($logs as $log) {
						$service->getDeliveryStatus($log->provider_response_id);
						$checked++;
					}
					return redirect()->back()->with('success', "وضعیت {$checked} پیامک بررسی شد.");
				}),
		];
	}
	
	protected function getHeaderWidgets(): array
	{
		return [
			\App\Filament\Widgets\SmsStatsWidget::class,
		];
	}
}
