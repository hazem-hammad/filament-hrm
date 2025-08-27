<?php

namespace App\Filament\Resources;

use App\Enum\StatusEnum;
use App\Filament\Resources\AdminResource\Pages;
use App\Models\Admin;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Admins';

    public static function getNavigationLabel(): string
    {
        return __('Admins');
    }

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Core';

    public static function getTitle(): string
    {
        return __('Admins');
    }

    public static function getModelLabel(): string
    {
        return __('Admin');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Admins');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([

                    TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(50),

                    TextInput::make('email')
                        ->label(__('Email'))
                        ->required()
                        ->email()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true),

                    Select::make('is_active')
                        ->label(__('Status'))
                        ->required()
                        ->options(StatusEnum::class)->default(StatusEnum::ACTIVE->value),

                    TextInput::make('password')
                        ->label(__('Password'))
                        ->password()
                        ->maxLength(50)

                        ->required(fn(Page $livewire) => ($livewire instanceof CreateRecord))
                        ->dehydrated(fn($state) => filled($state)),

                ])->columns(2)->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label(__('Status')),

                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->sortable()->hidden(),

            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->options(StatusEnum::class),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
