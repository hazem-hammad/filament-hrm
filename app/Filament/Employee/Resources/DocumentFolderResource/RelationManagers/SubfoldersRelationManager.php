<?php

namespace App\Filament\Employee\Resources\DocumentFolderResource\RelationManagers;

use App\Models\DocumentFolder;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubfoldersRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    protected static ?string $title = 'Subfolders';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Folder Name')
                    ->icon('heroicon-o-folder')
                    ->iconColor(fn(DocumentFolder $record): string => $record->color)
                    ->url(fn(DocumentFolder $record): string => 
                        \App\Filament\Employee\Resources\DocumentFolderResource::getUrl('view', ['record' => $record])
                    ),

                Tables\Columns\TextColumn::make('item_count')
                    ->label('Items')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('formatted_size')
                    ->label('Size')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
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
                Tables\Actions\Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-folder-open')
                    ->url(fn(DocumentFolder $record): string => 
                        \App\Filament\Employee\Resources\DocumentFolderResource::getUrl('view', ['record' => $record])
                    ),
            ])
            ->bulkActions([
                //
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_private', false));
    }
}