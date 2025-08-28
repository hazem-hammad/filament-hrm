<?php

namespace App\Filament\Employee\Resources\RequestResource\Pages;

use App\Filament\Employee\Resources\RequestResource;
use App\Models\VacationType;
use App\Models\AttendanceType;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateRequest extends CreateRecord
{
    protected static string $resource = RequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the employee_id to the authenticated employee
        $data['employee_id'] = auth('employee')->id();
        
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
        
        // Set default status
        $data['status'] = 'pending';
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $request = $this->record;
        
        // Validate the request using the model's validation logic
        $errors = $request->validateRequest();
        
        if (!empty($errors)) {
            // If there are validation errors, delete the created record and show errors
            $request->delete();
            
            foreach ($errors as $error) {
                Notification::make()
                    ->title('Validation Error')
                    ->body($error)
                    ->danger()
                    ->send();
            }
            
            // Redirect back to create page
            $this->redirect($this->getResource()::getUrl('create'));
            return;
        }
        
        // Send success notification
        Notification::make()
            ->title('Request Submitted')
            ->body('Your request has been submitted successfully and is awaiting approval.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}