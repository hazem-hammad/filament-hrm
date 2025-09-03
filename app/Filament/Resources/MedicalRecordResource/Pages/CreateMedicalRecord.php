<?php

namespace App\Filament\Resources\MedicalRecordResource\Pages;

use App\Filament\Resources\MedicalRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicalRecord extends CreateRecord
{
    protected static string $resource = MedicalRecordResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If employee_id is passed as a query parameter, use it
        if (request()->has('employee_id')) {
            $data['employee_id'] = request()->get('employee_id');
        }

        return $data;
    }

    protected function getFormActions(): array
    {
        $actions = parent::getFormActions();
        
        // If employee_id is passed, redirect back to employee view after creation
        if (request()->has('employee_id')) {
            $actions = array_map(function ($action) {
                if ($action->getName() === 'create') {
                    $action->after(function () {
                        $this->redirect(route('filament.admin.resources.employees.view', ['record' => request()->get('employee_id')]));
                    });
                }
                return $action;
            }, $actions);
        }

        return $actions;
    }
}
