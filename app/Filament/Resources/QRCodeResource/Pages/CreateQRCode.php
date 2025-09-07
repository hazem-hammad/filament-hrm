<?php

namespace App\Filament\Resources\QRCodeResource\Pages;

use App\Filament\Resources\QRCodeResource;
use App\Services\QRCodeService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQRCode extends CreateRecord
{
    protected static string $resource = QRCodeResource::class;
    
    protected function afterCreate(): void
    {
        $qrService = app(QRCodeService::class);
        $qrService->generateQRCode($this->record);
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
