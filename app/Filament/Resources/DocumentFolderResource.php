<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentFolderResource\Pages;
use App\Filament\Resources\DocumentFolderResource\RelationManagers;
use App\Models\DocumentFolder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentFolderResource extends Resource
{
    protected static ?string $model = DocumentFolder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'HR Setup';
    
    protected static ?string $navigationLabel = 'Document Manager';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Folder Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, Forms\Set $set) {
                                $set('name', str($state)->title());
                            })
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('parent_id')
                            ->label('Parent Folder')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\ColorPicker::make('color')
                            ->default('#1976D2')
                            ->required(),

                        Forms\Components\Toggle::make('is_private')
                            ->label('Private Folder')
                            ->default(false),

                        Forms\Components\Hidden::make('created_by')
                            ->default(fn() => auth('admin')->id()),
                    ])
                    ->columns(2),
            ]);
    }

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
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Parent Folder')
                    ->relationship('parent', 'name')
                    ->placeholder('All Folders'),

                Tables\Filters\TernaryFilter::make('is_private')
                    ->label('Privacy')
                    ->placeholder('All Folders')
                    ->trueLabel('Private Only')
                    ->falseLabel('Public Only'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),

                    Tables\Actions\EditAction::make(),

                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalDescription('This will move all contents to the parent folder or root.'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc');
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
}
