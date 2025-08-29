<?php

namespace App\Filament\Resources\JobResource\Pages;

use App\Filament\Resources\JobResource;
use App\Models\Job;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListJobs extends ListRecords
{
    protected static string $resource = JobResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['department', 'position', 'customQuestions']))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position.name')
                    ->label('Position')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number_of_positions')
                    ->label('Positions')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('work_type')
                    ->formatStateUsing(
                        fn(string $state): string =>
                        match ($state) {
                            'full_time' => 'Full Time',
                            'part_time' => 'Part Time',
                            'contract' => 'Contract',
                            'internship' => 'Internship',
                            default => $state
                        }
                    )
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('work_mode')
                    ->formatStateUsing(
                        fn(string $state): string =>
                        match ($state) {
                            'remote' => 'Remote',
                            'onsite' => 'Onsite',
                            'hybrid' => 'Hybrid',
                            default => $state
                        }
                    )
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('experience_level')
                    ->label('Experience')
                    ->formatStateUsing(
                        fn(string $state): string =>
                        match ($state) {
                            'entry' => 'Entry',
                            'junior' => 'Junior',
                            'mid' => 'Mid',
                            'senior' => 'Senior',
                            'lead' => 'Lead',
                            default => $state
                        }
                    )
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customQuestions')
                    ->label('Custom Questions')
                    ->formatStateUsing(
                        fn(Job $record): string =>
                        $record->customQuestions->count() > 0
                            ? $record->customQuestions->count() . ' questions'
                            : 'No questions'
                    )
                    ->badge()
                    ->color(
                        fn(Job $record): string =>
                        $record->customQuestions->count() > 0 ? 'success' : 'gray'
                    )
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('position_id')
                    ->label('Position')
                    ->relationship('position', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('work_type')
                    ->options([
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                        'internship' => 'Internship',
                    ]),
                Tables\Filters\SelectFilter::make('work_mode')
                    ->options([
                        'remote' => 'Remote',
                        'onsite' => 'Onsite',
                        'hybrid' => 'Hybrid',
                    ]),
                Tables\Filters\SelectFilter::make('experience_level')
                    ->options([
                        'entry' => 'Entry',
                        'junior' => 'Junior',
                        'mid' => 'Mid',
                        'senior' => 'Senior',
                        'lead' => 'Lead',
                    ]),
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
                        ->label(fn(Job $record): string => $record->status ? 'Deactivate' : 'Activate')
                        ->icon(fn(Job $record): string => $record->status ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn(Job $record): string => $record->status ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalDescription(
                            fn(Job $record): string =>
                            $record->status
                                ? 'Are you sure you want to deactivate this job?'
                                : 'Are you sure you want to activate this job?'
                        )
                        ->action(fn(Job $record) => $record->update(['status' => !$record->status])),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
            Actions\Action::make('careers')
                ->label('Careers')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('success')
                ->url('/careers')
                ->openUrlInNewTab(),
        ];
    }
}
