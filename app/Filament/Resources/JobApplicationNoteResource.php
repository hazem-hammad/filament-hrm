<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobApplicationNoteResource\Pages;
use App\Filament\Resources\JobApplicationNoteResource\RelationManagers;
use App\Models\JobApplicationNote;
use App\Models\JobApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JobApplicationNoteResource extends Resource
{
    protected static ?string $model = JobApplicationNote::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('job_application_id')
                    ->label('Job Application')
                    ->relationship('jobApplication', 'id')
                    ->getOptionLabelFromRecordUsing(fn (JobApplication $record): string => "{$record->full_name} - {$record->job->title}")
                    ->required()
                    ->searchable(),
                    
                Textarea::make('note')
                    ->label('Note/Comment')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                    
                Toggle::make('is_internal')
                    ->label('Internal Note')
                    ->helperText('Internal notes are only visible to staff members')
                    ->default(true),
                    
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jobApplication.full_name')
                    ->label('Applicant')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('jobApplication.job.title')
                    ->label('Job')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('note')
                    ->label('Note')
                    ->limit(50)
                    ->searchable(),
                    
                TextColumn::make('user.name')
                    ->label('Added By')
                    ->sortable(),
                    
                BooleanColumn::make('is_internal')
                    ->label('Internal'),
                    
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_internal')
                    ->label('Internal Notes Only')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListJobApplicationNotes::route('/'),
            'create' => Pages\CreateJobApplicationNote::route('/create'),
            'edit' => Pages\EditJobApplicationNote::route('/{record}/edit'),
        ];
    }
}
