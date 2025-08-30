<?php

namespace App\Filament\Resources\DocumentFolderResource\RelationManagers;

use App\Models\Document;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('documents')
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
                    ->required(),
                
                Forms\Components\Textarea::make('description')
                    ->maxLength(1000)
                    ->rows(3),
                
                Forms\Components\Select::make('assigned_to')
                    ->label('Assign to Employee')
                    ->options(Employee::query()->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                
                Forms\Components\Toggle::make('is_private')
                    ->label('Private Document')
                    ->default(false),

                Forms\Components\Hidden::make('created_by')
                    ->default(fn () => Auth::guard('admin')->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->icon(fn (Document $record): string => $record->file_icon)
                    ->iconColor(fn (Document $record): string => $record->file_color)
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('file_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (Document $record): string => $record->file_color)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('formatted_size')
                    ->label('Size')
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
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created by')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Upload File'),
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
                    
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No documents')
            ->emptyStateDescription('Upload files to this folder')
            ->emptyStateIcon('heroicon-o-document-plus');
    }
}