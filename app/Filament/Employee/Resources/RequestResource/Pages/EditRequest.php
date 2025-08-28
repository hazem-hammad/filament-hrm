<?php

namespace App\Filament\Employee\Resources\RequestResource\Pages;

use App\Filament\Employee\Resources\RequestResource;
use App\Models\VacationType;
use App\Models\AttendanceType;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditRequest extends EditRecord
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->status === 'pending'),
            Actions\Action::make('cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->action(function () {
                    $this->record->update(['status' => 'cancelled']);
                    
                    Notification::make()
                        ->title('Request Cancelled')
                        ->body('Your request has been cancelled successfully.')
                        ->success()
                        ->send();
                        
                    return redirect($this->getResource()::getUrl('index'));
                })
                ->requiresConfirmation()
                ->modalHeading('Cancel Request')
                ->modalDescription('Are you sure you want to cancel this request?')
                ->visible($this->record->status === 'pending'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Set the appropriate requestable_type based on request_type
        if ($data['request_type'] === 'vacation') {
            $data['requestable_type'] = VacationType::class;
        } elseif ($data['request_type'] === 'attendance') {
            $data['requestable_type'] = AttendanceType::class;
        }
        
        // Calculate total_days for vacation requests
        if ($data['request_type'] === 'vacation' && isset($data['start_date']) && isset($data['end_date'])) {
            $startDate = \Carbon\Carbon::parse($data['start_date']);
            $endDate = \Carbon\Carbon::parse($data['end_date']);
            $data['total_days'] = $startDate->diffInDays($endDate) + 1;
        }
        
        // Calculate hours for attendance requests
        if ($data['request_type'] === 'attendance' && isset($data['start_time']) && isset($data['end_time'])) {
            $startTime = \Carbon\Carbon::parse($data['start_time']);
            $endTime = \Carbon\Carbon::parse($data['end_time']);
            $data['hours'] = $endTime->diffInHours($startTime, true);
        }
        
        return $data;
    }

    protected function beforeSave(): void
    {
        // Only allow editing if request is pending
        if ($this->record->status !== 'pending') {
            Notification::make()
                ->title('Cannot Edit Request')
                ->body('You can only edit pending requests.')
                ->danger()
                ->send();
                
            $this->halt();
        }
        
        // Ensure employee can only edit their own requests
        if ($this->record->employee_id !== auth('employee')->id()) {
            Notification::make()
                ->title('Unauthorized')
                ->body('You can only edit your own requests.')
                ->danger()
                ->send();
                
            $this->halt();
        }
    }

    protected function afterSave(): void
    {
        $request = $this->record->fresh();
        
        // Validate the request using the model's validation logic
        $errors = $request->validateRequest();
        
        if (!empty($errors)) {
            foreach ($errors as $error) {
                Notification::make()
                    ->title('Validation Warning')
                    ->body($error)
                    ->warning()
                    ->send();
            }
        } else {
            Notification::make()
                ->title('Request Updated')
                ->body('Your request has been updated successfully.')
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}