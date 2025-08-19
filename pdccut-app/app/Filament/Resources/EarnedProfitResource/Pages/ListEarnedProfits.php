<?php

namespace App\Filament\Resources\EarnedProfitResource\Pages;

use App\Filament\Resources\EarnedProfitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEarnedProfits extends ListRecords
{
    protected static string $resource = EarnedProfitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
