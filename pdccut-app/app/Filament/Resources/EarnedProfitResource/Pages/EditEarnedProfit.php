<?php

namespace App\Filament\Resources\EarnedProfitResource\Pages;

use App\Filament\Resources\EarnedProfitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEarnedProfit extends EditRecord
{
    protected static string $resource = EarnedProfitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
