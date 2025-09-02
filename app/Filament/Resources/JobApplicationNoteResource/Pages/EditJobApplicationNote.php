<?php

namespace App\Filament\Resources\JobApplicationNoteResource\Pages;

use App\Filament\Resources\JobApplicationNoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobApplicationNote extends EditRecord
{
    protected static string $resource = JobApplicationNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
