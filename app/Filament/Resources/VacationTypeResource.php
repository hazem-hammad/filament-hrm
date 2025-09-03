<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VacationTypeResource\Pages;
use App\Filament\Resources\VacationTypeResource\RelationManagers;
use App\Models\VacationType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VacationTypeResource extends Resource
{
    protected static ?string $model = VacationType::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'HR Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                ])
                    ->schema([
                        // Basic Information Section
                        Forms\Components\Section::make('Basic Information')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Vacation Type Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Annual Leave, Sick Leave')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->placeholder('Describe this vacation type...')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('status')
                                    ->label('Active')
                                    ->default(true)
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        // Vacation Settings Section
                        Forms\Components\Section::make('Vacation Settings')
                            ->schema([
                                Forms\Components\TextInput::make('balance')
                                    ->label('Annual Balance')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(365)
                                    ->suffix('days')
                                    ->helperText('Total number of days available per year'),
                                Forms\Components\TextInput::make('unlock_after_months')
                                    ->label('Unlock After')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('months')
                                    ->default(0)
                                    ->helperText('Months after joining date (0 = immediately available)'),
                                Forms\Components\TextInput::make('required_days_before')
                                    ->label('Notice Period')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(90)
                                    ->suffix('days')
                                    ->default(0)
                                    ->helperText('Required notice days before vacation date'),
                                Forms\Components\Toggle::make('requires_approval')
                                    ->label('Requires Multi-Level Approval')
                                    ->default(false)
                                    ->helperText('Enable if this vacation type needs manager approval'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table;
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
            'index' => Pages\ListVacationTypes::route('/'),
            'create' => Pages\CreateVacationType::route('/create'),
            'view' => Pages\ViewVacationType::route('/{record}'),
            'edit' => Pages\EditVacationType::route('/{record}/edit'),
        ];
    }
}
