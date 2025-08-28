<?php

namespace App\Filament\Resources\VacationTypeResource\Pages;

use App\Filament\Resources\VacationTypeResource;
use App\Models\VacationType;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListVacationTypes extends ListRecords
{
    protected static string $resource = VacationTypeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Vacation Type')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Annual Balance')
                    ->numeric()
                    ->suffix(' days')
                    ->sortable()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('unlock_after_months')
                    ->label('Available After')
                    ->formatStateUsing(fn (int $state): string => 
                        $state === 0 ? 'Immediately' : $state . ' months'
                    )
                    ->badge()
                    ->color(fn (int $state): string => $state === 0 ? 'success' : 'warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('required_days_before')
                    ->label('Notice Period')
                    ->formatStateUsing(fn (int $state): string => 
                        $state === 0 ? 'No notice' : $state . ' days'
                    )
                    ->sortable(),
                Tables\Columns\IconColumn::make('requires_approval')
                    ->label('Needs Approval')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('warning')
                    ->falseColor('success'),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
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
                Tables\Filters\TernaryFilter::make('requires_approval')
                    ->boolean()
                    ->trueLabel('Requires Approval')
                    ->falseLabel('No Approval Required')
                    ->native(false),
                Tables\Filters\SelectFilter::make('unlock_after_months')
                    ->label('Availability')
                    ->options([
                        '0' => 'Immediately Available',
                        '1' => '1 Month',
                        '3' => '3 Months',
                        '6' => '6 Months',
                        '12' => '12 Months',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn(VacationType $record): string => $record->status ? 'Deactivate' : 'Activate')
                        ->icon(fn(VacationType $record): string => $record->status ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn(VacationType $record): string => $record->status ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalDescription(
                            fn(VacationType $record): string =>
                            $record->status
                                ? 'Are you sure you want to deactivate this vacation type?'
                                : 'Are you sure you want to activate this vacation type?'
                        )
                        ->action(fn(VacationType $record) => $record->update(['status' => !$record->status])),
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
