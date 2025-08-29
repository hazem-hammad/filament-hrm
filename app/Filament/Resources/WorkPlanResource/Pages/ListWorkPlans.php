<?php

namespace App\Filament\Resources\WorkPlanResource\Pages;

use App\Enum\WorkingDay;
use App\Filament\Resources\WorkPlanResource;
use App\Models\WorkPlan;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListWorkPlans extends ListRecords
{
    protected static string $resource = WorkPlanResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->withCount('employees'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Work Plan Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start Time')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('End Time')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('working_days_labels')
                    ->label('Working Days')
                    ->wrap()
                    ->searchable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('permission_minutes_label')
                    ->label('Grace Period')
                    ->badge()
                    ->color(fn($record): string => $record->permission_minutes > 0 ? 'warning' : 'gray')
                    ->searchable(false)
                    ->sortable('permission_minutes')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('employees_count')
                    ->label('Assigned Employees')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
                        ->label(fn(WorkPlan $record): string => $record->status ? 'Deactivate' : 'Activate')
                        ->icon(fn(WorkPlan $record): string => $record->status ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn(WorkPlan $record): string => ($record->status ?? false) ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalDescription(
                            fn(WorkPlan $record): string =>
                            $record->status
                                ? 'Are you sure you want to deactivate this work plan?'
                                : 'Are you sure you want to activate this work plan?'
                        )
                        ->action(fn(WorkPlan $record) => $record->update(['status' => !$record->status])),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn(WorkPlan $record): bool => $record->employees_count === 0)
                        ->modalDescription('This will permanently delete the work plan.'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
