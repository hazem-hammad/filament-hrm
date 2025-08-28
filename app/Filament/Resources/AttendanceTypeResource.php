<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceTypeResource\Pages;
use App\Filament\Resources\AttendanceTypeResource\RelationManagers;
use App\Models\AttendanceType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceTypeResource extends Resource
{
    protected static ?string $model = AttendanceType::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

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
                                    ->label('Attendance Type Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Overtime, Late Arrival, Early Leave')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->placeholder('Describe this attendance type...')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('status')
                                    ->label('Active')
                                    ->default(true)
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        // Limits & Restrictions Section
                        Forms\Components\Section::make('Limits & Restrictions')
                            ->schema([
                                Forms\Components\Toggle::make('has_limit')
                                    ->label('Has Limits')
                                    ->default(false)
                                    ->live()
                                    ->helperText('Enable to set monthly and per-request limits')
                                    ->columnSpanFull(),
                                
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('max_hours_per_month')
                                            ->label('Max Hours per Month')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(744) // Max hours in a month (31 days * 24 hours)
                                            ->suffix('hours')
                                            ->placeholder('e.g., 40')
                                            ->helperText('Maximum total hours allowed per month')
                                            ->visible(fn (Forms\Get $get) => $get('has_limit')),
                                        Forms\Components\TextInput::make('max_requests_per_month')
                                            ->label('Max Requests per Month')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('requests')
                                            ->placeholder('e.g., 10')
                                            ->helperText('Maximum number of requests per month')
                                            ->visible(fn (Forms\Get $get) => $get('has_limit')),
                                        Forms\Components\TextInput::make('max_hours_per_request')
                                            ->label('Max Hours per Request')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(24)
                                            ->step(0.25)
                                            ->suffix('hours')
                                            ->placeholder('e.g., 8.5')
                                            ->helperText('Maximum hours allowed per single request')
                                            ->visible(fn (Forms\Get $get) => $get('has_limit'))
                                            ->columnSpan(2),
                                    ]),

                                Forms\Components\Toggle::make('requires_approval')
                                    ->label('Requires Multi-Level Approval')
                                    ->default(false)
                                    ->helperText('Enable if this attendance type needs manager approval')
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
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
            'index' => Pages\ListAttendanceTypes::route('/'),
            'create' => Pages\CreateAttendanceType::route('/create'),
            'view' => Pages\ViewAttendanceType::route('/{record}'),
            'edit' => Pages\EditAttendanceType::route('/{record}/edit'),
        ];
    }
}
