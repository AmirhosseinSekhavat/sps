<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SmsLogsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
	protected EloquentBuilder $query;

	public function __construct(EloquentBuilder $query)
	{
		$this->query = $query;
	}

	public function query(): EloquentBuilder
	{
		return $this->query;
	}

	public function headings(): array
	{
		return [
			'شناسه',
			'کد ملی',
			'شماره موبایل',
			'نوع',
			'وضعیت',
			'متن/کد',
			'ارائه‌دهنده',
			'شناسه پاسخ ارائه‌دهنده',
			'IP',
			'تاریخ ایجاد',
			'زمان ارسال',
			'زمان تحویل',
		];
	}

	public function map($log): array
	{
		$typedText = $log->type === 'otp' ? ($log->otp_code ?? '') : ($log->message ?? '');
		return [
			$log->id,
			$log->national_code,
			$log->mobile_number,
			match ($log->type) {
				'otp' => 'کد تایید',
				'notification' => 'اعلان',
				default => $log->type,
			},
			match ($log->status) {
				'pending' => 'در انتظار',
				'sent' => 'ارسال شده',
				'failed' => 'ناموفق',
				'delivered' => 'تحویل شده',
				default => $log->status,
			},
			$typedText,
			$log->provider,
			$log->provider_response_id,
			$log->ip_address,
			optional($log->created_at)?->format('Y-m-d H:i'),
			optional($log->sent_at)?->format('Y-m-d H:i'),
			optional($log->delivered_at)?->format('Y-m-d H:i'),
		];
	}

	public function styles(Worksheet $sheet)
	{
		return [
			1 => [
				'font' => ['bold' => true],
			],
		];
	}
} 