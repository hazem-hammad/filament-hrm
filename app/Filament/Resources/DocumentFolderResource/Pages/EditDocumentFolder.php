<?php

namespace App\Filament\Resources\DocumentFolderResource\Pages;

use App\Filament\Resources\DocumentFolderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentFolder extends EditRecord
{
    protected static string $resource = DocumentFolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
