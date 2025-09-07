<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\DocumentFolderResource\Pages;
use App\Filament\Employee\Resources\DocumentFolderResource\RelationManagers;
use App\Models\DocumentFolder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentFolderResource extends Resource
{
    protected static ?string $model = DocumentFolder::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = 'Company Documents';
    
    protected static ?string $navigationGroup = 'Documents';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Folder')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->icon('heroicon-o-folder')
                    ->iconColor(fn(DocumentFolder $record): string => $record->color),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent Folder')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Root'),

                Tables\Columns\TextColumn::make('item_count')
                    ->label('Items')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('formatted_size')
                    ->label('Size')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created by')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Parent Folder')
                    ->relationship('parent', 'name')
                    ->placeholder('All Folders'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Open Folder'),
            ])
            ->defaultSort('name', 'asc')
            ->emptyStateHeading('No folders found')
            ->emptyStateDescription('No public folders are available.')
            ->emptyStateIcon('heroicon-o-folder-open');
    }

    public static function getEloquentQuery(): Builder
    {
        // Only show public folders
        return parent::getEloquentQuery()->where('is_private', false);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubfoldersRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocumentFolders::route('/'),
            'view' => Pages\ViewDocumentFolder::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}