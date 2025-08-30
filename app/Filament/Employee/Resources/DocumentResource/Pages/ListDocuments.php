<?php

namespace App\Filament\Employee\Resources\DocumentResource\Pages;

use App\Filament\Employee\Resources\DocumentResource;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    public function getTitle(): string
    {
        return 'My Files';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}