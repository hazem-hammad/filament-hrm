<?php

namespace App\Filament\Resources\CustomQuestionResource\Pages;

use App\Filament\Resources\CustomQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomQuestion extends EditRecord
{
    protected static string $resource = CustomQuestionResource::class;
}
