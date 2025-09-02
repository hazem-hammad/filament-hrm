<?php

namespace App\Filament\Resources\JobApplicationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';

    protected static ?string $title = 'Notes & Comments';

    protected static ?string $icon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static bool $isLazy = false;

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('note')
                    ->label('Note/Comment')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                SpatieMediaLibraryFileUpload::make('attachments')
                    ->label('Attachments')
                    ->collection('attachments')
                    ->multiple()
                    ->columnSpanFull()
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/*', 'text/plain'])
                    ->maxSize(10 * 1024) // 10MB
                    ->helperText('You can upload multiple files (PDF, Word, images, text files). Max size: 10MB per file.')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('note')
            ->columns([
                TextColumn::make('note')
                    ->label('Note')
                    ->limit(100)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('user.name')
                    ->label('Added By')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Note')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $userId = auth()->id();
                        if (!$userId) {
                            throw new \Exception('User must be authenticated to add notes.');
                        }

                        // Verify user exists in database
                        $userExists = \App\Models\User::where('id', $userId)->exists();
                        if (!$userExists) {
                            throw new \Exception('Authenticated user not found in database. Please contact administrator.');
                        }

                        $data['user_id'] = $userId;
                        $data['is_internal'] = true; // Default to internal since no toggle
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No notes yet')
            ->emptyStateDescription('Add your first note or comment about this job application.')
            ->emptyStateIcon('heroicon-o-chat-bubble-left-ellipsis');
    }
}
