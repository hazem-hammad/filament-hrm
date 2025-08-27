<?php

namespace App\Filament\Resources\JobApplicationResource\Pages;

use App\Filament\Resources\JobApplicationResource;
use App\Models\JobApplication;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

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
                    Tables\Actions\EditAction::make(),
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
                    Tables\Actions\DeleteBulkAction::make(),
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
}
