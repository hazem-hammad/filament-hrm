<?php

namespace App\Filament\Resources\JobApplicationNoteResource\Pages;

use App\Filament\Resources\JobApplicationNoteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobApplicationNotes extends ListRecords
{
    protected static string $resource = JobApplicationNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
