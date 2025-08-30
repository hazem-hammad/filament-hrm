<?php

namespace App\Filament\Resources\DocumentFolderResource\Pages;

use App\Filament\Resources\DocumentFolderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentFolders extends ListRecords
{
    protected static string $resource = DocumentFolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
