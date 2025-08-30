<?php

namespace App\Filament\Employee\Resources\DocumentFolderResource\Pages;

use App\Filament\Employee\Resources\DocumentFolderResource;
use Filament\Resources\Pages\ListRecords;

class ListDocumentFolders extends ListRecords
{
    protected static string $resource = DocumentFolderResource::class;

    public function getTitle(): string
    {
        return 'Company Documents';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}