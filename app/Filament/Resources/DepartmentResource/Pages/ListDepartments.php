<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use App\Models\Department;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListDepartments extends ListRecords
{
    protected static string $resource = DepartmentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn(Department $record): string => $record->status ? 'Deactivate' : 'Activate')
                        ->icon(fn(Department $record): string => $record->status ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn(Department $record): string => ($record->status ?? false) ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalDescription(
                            fn(Department $record): string =>
                            $record->status
                                ? 'Are you sure you want to deactivate this department?'
                                : 'Are you sure you want to activate this department?'
                        )
                        ->action(fn(Department $record) => $record->update(['status' => !$record->status])),
                    Tables\Actions\DeleteAction::make(),
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
