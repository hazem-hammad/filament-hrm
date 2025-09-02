<?php

namespace App\Filament\Resources\JobApplicationResource\Pages;

use App\Filament\Resources\JobApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewJobApplication extends ViewRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load the application with its relationships
        $application = static::getResource()::getModel()::with(['job', 'jobStage', 'answers.customQuestion'])
            ->find($this->getRecord()->getKey());

        return $application->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil'),
        ];
    }
    
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
