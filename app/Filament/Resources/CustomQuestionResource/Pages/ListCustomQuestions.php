<?php

namespace App\Filament\Resources\CustomQuestionResource\Pages;

use App\Filament\Resources\CustomQuestionResource;
use App\Models\CustomQuestion;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListCustomQuestions extends ListRecords
{
    protected static string $resource = CustomQuestionResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn(string $state): string => 
                        match($state) {
                            'text_field' => 'Text Field',
                            'date' => 'Date',
                            'textarea' => 'Textarea',
                            'file_upload' => 'File Upload',
                            'toggle' => 'Toggle',
                            'multi_select' => 'Multi Select',
                            default => $state
                        }
                    )
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean(),
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
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'text_field' => 'Text Field',
                        'date' => 'Date',
                        'textarea' => 'Textarea',
                        'file_upload' => 'File Upload',
                        'toggle' => 'Toggle',
                        'multi_select' => 'Multi Select',
                    ]),
                Tables\Filters\TernaryFilter::make('is_required')
                    ->label('Required')
                    ->boolean()
                    ->trueLabel('Required')
                    ->falseLabel('Optional')
                    ->native(false),
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
                        ->label(fn(CustomQuestion $record): string => $record->status ? 'Deactivate' : 'Activate')
                        ->icon(fn(CustomQuestion $record): string => $record->status ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn(CustomQuestion $record): string => $record->status ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalDescription(
                            fn(CustomQuestion $record): string =>
                            $record->status
                                ? 'Are you sure you want to deactivate this custom question?'
                                : 'Are you sure you want to activate this custom question?'
                        )
                        ->action(fn(CustomQuestion $record) => $record->update(['status' => !$record->status])),
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
            Actions\CreateAction::make(),
        ];
    }
}
