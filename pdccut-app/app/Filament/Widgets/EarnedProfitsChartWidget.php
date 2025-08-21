<?php

namespace App\Filament\Widgets;

use App\Models\EarnedProfit;
use Filament\Widgets\ChartWidget;

class EarnedProfitsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'نمودار سودهای اکتسابی عملکرد شرکت';
    
    protected static ?string $maxHeight = '320px';

    protected int | string | array $columnSpan = [
        'sm' => 12,
        'lg' => 6,
    ];
    
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $rows = EarnedProfit::active()
            ->orderBy('year')
            ->get(['year', 'amount']);

        $years = $rows->pluck('year')->map(fn ($y) => (string) $y)->all();
        $profits = $rows->pluck('amount')->map(fn ($v) => (float) $v)->all();

        return [
            'datasets' => [
                [
                    'label' => 'سودهای اکتسابی (هزار ریال)',
                    'data' => $profits,
                    'backgroundColor' => 'rgba(220, 38, 38, 0.8)',
                    'borderColor' => 'rgba(220, 38, 38, 1)',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                    'borderSkipped' => false,
                    'barThickness' => 16,
                    'maxBarThickness' => 22,
                ],
            ],
            'labels' => $years,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'layout' => [
                'padding' => [ 'left' => 8, 'right' => 8, 'top' => 8, 'bottom' => 8 ],
            ],
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'نمودار سودهای اکتسابی عملکرد شرکت (سال‌های دارای داده)',
                    'font' => [
                        'size' => 16,
                        'family' => 'Vazir, Tahoma, sans-serif'
                    ]
                ],
                'legend' => [
                    'display' => false
                ]
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
                'x' => [
                    'ticks' => [ 'autoSkip' => true, 'maxRotation' => 0 ],
                ]
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
