<?php

namespace App\Filament\Resources\QRCodeResource\Pages;

use App\Filament\Resources\QRCodeResource;
use App\Services\QRCodeService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQRCode extends EditRecord
{
    protected static string $resource = QRCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download')
                ->label('Download QR Code')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->action(function () {
                    $qrService = app(QRCodeService::class);
                    return $qrService->downloadQRCode($this->record);
                })
                ->visible(fn () => $this->record->qr_code_path !== null),
            Actions\Action::make('regenerate')
                ->label('Regenerate QR Code')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Regenerate QR Code')
                ->modalDescription('This will generate a new QR code. The old QR code will be replaced.')
                ->action(function () {
                    $qrService = app(QRCodeService::class);
                    $qrService->regenerateQRCode($this->record);
                }),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function afterSave(): void
    {
        // Regenerate QR code if contact details changed
        if ($this->record->wasChanged(['name', 'phone', 'phone_2', 'email', 'website'])) {
            $qrService = app(QRCodeService::class);
            $qrService->regenerateQRCode($this->record);
        }
    }
}
