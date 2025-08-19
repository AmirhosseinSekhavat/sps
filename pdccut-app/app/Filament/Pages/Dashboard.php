<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int | array
    {
        return [
            'sm' => 1,
            'lg' => 12,
        ];
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\UserStatsWidget::class,
            \App\Filament\Widgets\UserGrowthChart::class,
            \App\Filament\Widgets\EarnedProfitsChartWidget::class,
            \App\Filament\Widgets\LatestActivities::class,
        ];
    }
} 