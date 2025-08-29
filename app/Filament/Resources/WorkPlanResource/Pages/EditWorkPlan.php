<?php

namespace App\Filament\Resources\WorkPlanResource\Pages;

use App\Filament\Resources\WorkPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkPlan extends EditRecord
{
    protected static string $resource = WorkPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->icon('heroicon-o-eye'),
            Actions\DeleteAction::make()
                ->visible(fn(): bool => $this->record->employees()->count() === 0)
                ->modalDescription('This will permanently delete the work plan.')
                ->icon('heroicon-o-trash'),
        ];
    }
}
