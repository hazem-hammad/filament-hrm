<?php

namespace App\Filament\Resources\DocumentFolderResource\RelationManagers;

use App\Models\DocumentFolder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SubfoldersRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    protected static ?string $title = 'Subfolders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $state, \Filament\Forms\Set $set) {
                        $set('name', str($state)->title());
                    }),
                
                Forms\Components\Textarea::make('description')
                    ->maxLength(1000)
                    ->rows(3),
                
                Forms\Components\ColorPicker::make('color')
                    ->default('#1976D2')
                    ->required(),
                
                Forms\Components\Toggle::make('is_private')
                    ->label('Private Folder')
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
                    ->icon('heroicon-o-folder')
                    ->iconColor(fn (DocumentFolder $record): string => $record->color)
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('item_count')
                    ->label('Items')
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('formatted_size')
                    ->label('Size')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\IconColumn::make('is_private')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created by')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_private')
                    ->label('Privacy')
                    ->placeholder('All Folders')
                    ->trueLabel('Private Only')
                    ->falseLabel('Public Only'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Folder'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->icon('heroicon-o-eye')
                        ->url(fn (DocumentFolder $record): string => 
                            \App\Filament\Resources\DocumentFolderResource::getUrl('view', ['record' => $record])
                        ),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No subfolders')
            ->emptyStateDescription('Create a new folder to organize your documents')
            ->emptyStateIcon('heroicon-o-folder-open');
    }
}