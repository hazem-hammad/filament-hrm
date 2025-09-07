<?php

namespace App\Filament\Resources\QRCodeResource\Pages;

use App\Filament\Resources\QRCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQRCodes extends ListRecords
{
    protected static string $resource = QRCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
