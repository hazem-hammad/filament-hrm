<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Details')
                    ->schema([
                        \Filament\Forms\Components\SpatieMediaLibraryFileUpload::make('documents')
                            ->collection('documents')
                            ->required()
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'text/plain',
                                'image/*',
                                'video/*',
                                'audio/*',
                                'application/zip',
                            ])
                            ->maxSize(50 * 1024),
                        
                        Forms\Components\TextInput::make('name')
                            ->label('Document Name')
                            ->maxLength(255)
                            ->live(),
                        
                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->rows(3),
                        
                        Forms\Components\Select::make('folder_id')
                            ->label('Folder')
                            ->relationship('folder', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('Root Directory'),
                        
                        Forms\Components\Select::make('assigned_to')
                            ->label('Assign to Employee')
                            ->relationship('assignedEmployee', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        
                        Forms\Components\Toggle::make('is_private')
                            ->label('Private Document')
                            ->default(false),
                            
                        Forms\Components\Hidden::make('created_by')
                            ->default(fn () => auth('admin')->id()),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                
                Tables\Columns\TextColumn::make('folder.name')
                    ->label('Folder')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Root'),
                
                Tables\Columns\TextColumn::make('file_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (Document $record): string => $record->file_color)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('formatted_size')
                    ->label('Size')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('assignedEmployee.name')
                    ->label('Assigned to')
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\IconColumn::make('is_private')
                    ->label('Private')
                    ->boolean()
                    ->alignCenter(),
                
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(fn (Document $record): string => $record->getFirstMediaUrl('documents'))
                        ->openUrlInNewTab(),
                    
                    Tables\Actions\Action::make('preview')
                        ->icon('heroicon-o-eye')
                        ->visible(fn (Document $record): bool => $record->can_preview)
                        ->modalContent(function (Document $record) {
                            if ($record->file_type === 'pdf') {
                                return view('filament.components.pdf-preview', ['url' => $record->getFirstMediaUrl('documents')]);
                            }
                            return null;
                        }),
                    
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
