<?php

namespace App\Filament\Widgets;

use App\Models\ShareCertificate;
use Filament\Forms;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ShareCertificatesStats extends BaseWidget
{
	public ?int $year = null;

	protected function getFormSchema(): array
	{
		return [
			Forms\Components\Select::make('year')
				->label('سال')
				->options(function () {
					return ShareCertificate::query()
						->select('year')
						->distinct()
						->pluck('year', 'year')
						->sort()
						->reverse();
				})
				->placeholder('همه سال‌ها')
				->native(false)
				->live(),
		];
	}

	private function getActiveYear(): ?int
	{
		if (!empty($this->year)) {
			return (int) $this->year;
		}
		// Prefer session value set by table filter
		if (session()->has('sc_year')) {
			return (int) session('sc_year');
		}
		return null;
	}

	protected function getStats(): array
	{
		$activeYear = $this->getActiveYear();

		$query = ShareCertificate::query();
		if ($activeYear) {
			$query->where('year', $activeYear);
		}

		$total = (clone $query)->count();
		$sumShareAmount = (clone $query)->sum('share_amount');

		// Window-function approach: pick exactly the latest row per user (by year desc, id desc)
		$ranked = DB::table('share_certificates')
			->select(
				'id',
				'user_id',
				'year',
				'share_count',
				DB::raw('ROW_NUMBER() OVER (PARTITION BY user_id ORDER BY year DESC, id DESC) as rn')
			);

		$latestRows = DB::query()->fromSub($ranked, 'r')
			->where('rn', 1)
			->when($activeYear, fn($q) => $q->where('year', $activeYear));

		$sumShareCount = (int) ($latestRows->sum('share_count') ?? 0);

		$sumAnnualPayment = (clone $query)->sum('annual_payment');

		$latestYear = ShareCertificate::max('year');
		$latestYearCount = $latestYear ? ShareCertificate::where('year', $latestYear)->count() : 0;

		$descScope = $activeYear ? ('سال ' . $activeYear) : 'همه سال‌ها';

		return [
			Stat::make('تعداد برگه‌ها', number_format($total))
				->description($descScope)
				->color('primary')
				->icon('heroicon-o-document-text'),
			Stat::make('جمع مبلغ سهام', number_format($sumShareAmount) . ' ریال')
				->description($descScope)
				->color('info')
				->icon('heroicon-o-banknotes'),
			Stat::make('جمع تعداد سهام', number_format($sumShareCount))
				->description('بر اساس آخرین سال هر سهامدار' . ($activeYear ? ' - سال ' . $activeYear : ''))
				->color('warning')
				->icon('heroicon-o-chart-bar'),
			Stat::make('سود پرداختی سال', number_format($sumAnnualPayment) . ' ریال')
				->description($descScope)
				->color('success')
				->icon('heroicon-o-currency-dollar'),
			Stat::make('تعداد برگه‌های سال اخیر', number_format($latestYearCount))
				->description($latestYear ? ('سال ' . $latestYear) : '—')
				->color('gray')
				->icon('heroicon-o-calendar'),
		];
	}
} 