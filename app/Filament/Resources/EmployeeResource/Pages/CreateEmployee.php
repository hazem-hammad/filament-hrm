<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Notifications\EmployeeWelcomeNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate temporary password
        $temporaryPassword = Str::random(12);
        
        // Hash the temporary password
        if (isset($data['password'])) {
            $data['password'] = Hash::make($temporaryPassword);
        }
        
        // Store the temporary password for email
        $this->temporaryPassword = $temporaryPassword;
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Generate password setup token
        $token = Str::random(64);
        
        // Store the token in cache for 24 hours
        cache()->put("employee_password_setup_{$token}", [
            'employee_id' => $this->record->id,
            'created_at' => now()
        ], now()->addDay());
        
        // Send welcome email
        $this->record->notify(new EmployeeWelcomeNotification(
            $this->temporaryPassword,
            $token
        ));
    }

    private string $temporaryPassword;
}