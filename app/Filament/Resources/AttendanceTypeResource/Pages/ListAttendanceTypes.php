<?php

namespace App\Filament\Resources\AttendanceTypeResource\Pages;

use App\Filament\Resources\AttendanceTypeResource;
use App\Models\AttendanceType;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListAttendanceTypes extends ListRecords
{
    protected static string $resource = AttendanceTypeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Attendance Type')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('limit_summary')
                    ->label('Limits')
                    ->badge()
                    ->color(fn (AttendanceType $record): string => $record->has_limit ? 'warning' : 'success')
                    ->sortable(['has_limit']),
                Tables\Columns\TextColumn::make('max_hours_per_month')
                    ->label('Max Hours/Month')
                    ->formatStateUsing(fn (?int $state): string => 
                        $state ? $state . 'h' : 'No limit'
                    )
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('max_requests_per_month')
                    ->label('Max Requests/Month')
                    ->formatStateUsing(fn (?int $state): string => 
                        $state ? $state . ' req' : 'No limit'
                    )
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('max_hours_per_request')
                    ->label('Max Hours/Request')
                    ->formatStateUsing(fn (?float $state): string => 
                        $state ? $state . 'h' : 'No limit'
                    )
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\TernaryFilter::make('has_limit')
                    ->boolean()
                    ->trueLabel('Limited')
                    ->falseLabel('Unlimited')
                    ->native(false),
                Tables\Filters\TernaryFilter::make('requires_approval')
                    ->boolean()
                    ->trueLabel('Requires Approval')
                    ->falseLabel('No Approval Required')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn(AttendanceType $record): string => $record->status ? 'Deactivate' : 'Activate')
                        ->icon(fn(AttendanceType $record): string => $record->status ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn(AttendanceType $record): string => $record->status ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalDescription(
                            fn(AttendanceType $record): string =>
                            $record->status
                                ? 'Are you sure you want to deactivate this attendance type?'
                                : 'Are you sure you want to activate this attendance type?'
                        )
                        ->action(fn(AttendanceType $record) => $record->update(['status' => !$record->status])),
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
