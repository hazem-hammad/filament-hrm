<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditRequest extends EditRecord
{
    protected static string $resource = RequestResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['request_type'] === 'vacation' && isset($data['start_date']) && isset($data['end_date'])) {
            $data['total_days'] = $this->calculateTotalDays($data['start_date'], $data['end_date']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Request Updated')
            ->body('The request has been updated successfully.')
            ->success()
            ->send();
    }

    private function calculateTotalDays(string $startDate, string $endDate): int
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        
        return $start->diffInDays($end) + 1;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->icon('heroicon-o-eye'),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
