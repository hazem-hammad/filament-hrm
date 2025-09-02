<?php

namespace App\Filament\Resources\JobApplicationNoteResource\Pages;

use App\Filament\Resources\JobApplicationNoteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJobApplicationNote extends CreateRecord
{
    protected static string $resource = JobApplicationNoteResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
