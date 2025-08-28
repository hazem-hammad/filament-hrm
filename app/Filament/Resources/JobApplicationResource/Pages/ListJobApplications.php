<?php

namespace App\Filament\Resources\JobApplicationResource\Pages;

use App\Filament\Resources\JobApplicationResource;
use App\Models\JobApplication;
use App\Models\JobStage;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListJobApplications extends ListRecords
{
    protected static string $resource = JobApplicationResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['job', 'jobStage']))
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Applicant Name')
                    ->getStateUsing(fn (JobApplication $record): string => $record->first_name . ' ' . $record->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job.title')
                    ->label('Job Position')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jobStage.name')
                    ->label('Current Stage')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('years_of_experience')
                    ->label('Experience')
                    ->numeric()
                    ->suffix(' years')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('job_id')
                    ->label('Job Position')
                    ->relationship('job', 'title')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('job_stage_id')
                    ->label('Application Stage')
                    ->relationship('jobStage', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('status')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\Action::make('change_stage')
                        ->label('Change Stage')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('job_stage_id')
                                ->label('New Stage')
                                ->options(function () {
                                    return JobStage::query()
                                        ->active()
                                        ->sorted()
                                        ->pluck('name', 'id');
                                })
                                ->required()
                                ->searchable()
                                ->preload()
                                ->default(fn(JobApplication $record) => $record->job_stage_id),
                        ])
                        ->action(function (array $data, JobApplication $record): void {
                            $oldStage = $record->jobStage->name;
                            $newStage = JobStage::find($data['job_stage_id'])->name;
                            
                            $record->update([
                                'job_stage_id' => $data['job_stage_id']
                            ]);
                            
                            Notification::make()
                                ->title('Stage Updated')
                                ->body("Application stage changed from '{$oldStage}' to '{$newStage}'")
                                ->success()
                                ->send();
                        })
                        ->modalHeading('Change Application Stage')
                        ->modalDescription('Select a new stage for this job application.')
                        ->modalWidth('md'),
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn(JobApplication $record): string => $record->status ? 'Deactivate' : 'Activate')
                        ->icon(fn(JobApplication $record): string => $record->status ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn(JobApplication $record): string => $record->status ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalDescription(
                            fn(JobApplication $record): string =>
                            $record->status
                                ? 'Are you sure you want to deactivate this application?'
                                : 'Are you sure you want to activate this application?'
                        )
                        ->action(fn(JobApplication $record) => $record->update(['status' => !$record->status])),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];
        
        // Add "All Applications" tab
        $tabs['all'] = Tab::make('All Applications')
            ->badge(JobApplication::count());

        // Get all active job stages sorted by their sort order
        $jobStages = JobStage::query()
            ->active()
            ->sorted()
            ->get();

        // Create a tab for each job stage
        foreach ($jobStages as $stage) {
            $tabs['stage_' . $stage->id] = Tab::make($stage->name)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('job_stage_id', $stage->id))
                ->badge(JobApplication::where('job_stage_id', $stage->id)->count());
        }

        return $tabs;
    }
}
