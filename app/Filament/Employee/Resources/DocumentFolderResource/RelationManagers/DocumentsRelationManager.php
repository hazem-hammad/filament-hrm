<?php

namespace App\Filament\Employee\Resources\DocumentFolderResource\RelationManagers;

use App\Models\Document;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documents';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\IconColumn::make('file_icon')
                        ->label('File')
                        ->alignLeft()
                        ->icon(fn (Document $record): string => $record->file_icon)
                        ->color(fn (Document $record): string => $record->file_color),
                    
                    Tables\Columns\TextColumn::make('name')
                        ->searchable()
                        ->sortable()
                        ->weight('medium'),
                ])->space(2),

                Tables\Columns\TextColumn::make('file_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (Document $record): string => $record->file_color),

                Tables\Columns\TextColumn::make('formatted_size')
                    ->label('Size')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('assignedEmployee.name')
                    ->label('Assigned to')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->label('Download')
                    ->url(fn (Document $record): string => $record->getFirstMediaUrl('documents'))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('preview')
                    ->icon('heroicon-o-eye')
                    ->label('Preview')
                    ->visible(fn (Document $record): bool => $record->can_preview)
                    ->modalContent(function (Document $record) {
                        if ($record->file_type === 'pdf') {
                            return view('filament.components.pdf-preview', ['url' => $record->getFirstMediaUrl('documents')]);
                        }
                        return null;
                    }),
            ])
            ->bulkActions([
                //
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_private', false));
    }
}