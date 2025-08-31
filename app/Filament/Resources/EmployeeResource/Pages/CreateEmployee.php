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
        // Generate temporary password (12 characters with mixed case, numbers and symbols)
        $this->temporaryPassword = Str::password(12, true, true, true, false);
        
        // Hash the temporary password for storage
        $data['password'] = Hash::make($this->temporaryPassword);
        $data['password_set_at'] = now();
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Send welcome email with login credentials
        $this->record->notify(new EmployeeWelcomeNotification(
            $this->temporaryPassword,
            ''  // No longer using password setup token
        ));
    }

    private string $temporaryPassword;
}