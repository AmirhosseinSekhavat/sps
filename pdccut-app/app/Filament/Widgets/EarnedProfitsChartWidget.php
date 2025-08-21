<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\EarnedProfit;

class EarnedProfitsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'سودهای اکتسابی (هزار ریال)';

    protected function getData(): array
    {
        $profits = EarnedProfit::active()->orderBy('year')->get(['year', 'amount']);
        $labels = $profits->pluck('year')->toArray();
        $data = $profits->map(fn($p) => round($p->amount / 1000, 2))->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'سودها (هزار ریال)',
                    'data' => $data,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
