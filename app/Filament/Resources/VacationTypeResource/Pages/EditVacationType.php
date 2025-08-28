<?php

namespace App\Filament\Resources\VacationTypeResource\Pages;

use App\Filament\Resources\VacationTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVacationType extends EditRecord
{
    protected static string $resource = VacationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
