<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\DocumentResource\Pages;
use App\Models\Document;
use App\Models\DocumentFolder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = 'My Files';
    
    protected static ?string $navigationGroup = 'Documents';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\IconColumn::make('file_icon')
                    ->label('File')
                    ->alignLeft()
                    ->icon(fn(Document $record): string => $record->file_icon)
                    ->color(fn(Document $record): string => $record->file_color),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('folder.name')
                    ->label('Folder')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Root'),

                Tables\Columns\TextColumn::make('file_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(Document $record): string => $record->file_color)
                    ->sortable(),

                Tables\Columns\TextColumn::make('formatted_size')
                    ->label('Size')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('folder_id')
                    ->label('Folder')
                    ->relationship('folder', 'name')
                    ->placeholder('All Folders'),

                Tables\Filters\SelectFilter::make('file_type')
                    ->label('File Type')
                    ->options([
                        'pdf' => 'PDF',
                        'doc' => 'Word Document',
                        'docx' => 'Word Document',
                        'xls' => 'Excel',
                        'xlsx' => 'Excel',
                        'jpg' => 'Image',
                        'png' => 'Image',
                        'mp4' => 'Video',
                        'zip' => 'Archive',
                    ]),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Assigned Employee')
                    ->relationship('assignedEmployee', 'name')
                    ->placeholder('All Documents'),

                Tables\Filters\TernaryFilter::make('is_private')
                    ->label('Privacy')
                    ->placeholder('All Documents')
                    ->trueLabel('Private Only')
                    ->falseLabel('Public Only'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->label('Download')
                    ->url(fn(Document $record): string => $record->getFirstMediaUrl('documents'))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No documents found')
            ->emptyStateDescription('No documents have been assigned to you yet.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public static function getEloquentQuery(): Builder
    {
        // Only show documents assigned to the current employee
        return parent::getEloquentQuery()->where('assigned_to', auth('employee')->id());
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
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
