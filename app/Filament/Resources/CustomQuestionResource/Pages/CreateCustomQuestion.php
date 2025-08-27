<?php

namespace App\Filament\Resources\CustomQuestionResource\Pages;

use App\Filament\Resources\CustomQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomQuestion extends CreateRecord
{
    protected static string $resource = CustomQuestionResource::class;
}
