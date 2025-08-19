<?php

namespace App\Filament\Resources\ShareCertificateResource\Pages;

use App\Filament\Resources\ShareCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShareCertificates extends ListRecords
{
    protected static string $resource = ShareCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\ShareCertificatesStats::class,
        ];
    }
}
