<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use App\Models\Request;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateRequest extends CreateRecord
{
    protected static string $resource = RequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['request_type'] === 'vacation') {
            $data['total_days'] = $this->calculateTotalDays($data['start_date'], $data['end_date']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Request Submitted')
            ->body('Your request has been submitted successfully and is pending approval.')
            ->success()
            ->send();
    }

    private function calculateTotalDays(string $startDate, string $endDate): int
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        
        return $start->diffInDays($end) + 1;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
