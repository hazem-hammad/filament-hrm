<?php

namespace App\Filament\Resources;

use App\Enum\WorkingDay;
use App\Filament\Resources\WorkPlanResource\Pages;
use App\Models\WorkPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkPlanResource extends Resource
{
    protected static ?string $model = WorkPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'HR Setup';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Work Plan Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Work Plan Name')
                            ->placeholder('Enter work plan name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Start Time')
                            ->required()
                            ->seconds(false),
                        Forms\Components\TimePicker::make('end_time')
                            ->label('End Time')
                            ->required()
                            ->seconds(false)
                            ->after('start_time'),
                        Forms\Components\CheckboxList::make('working_days')
                            ->label('Working Days')
                            ->options(WorkingDay::options())
                            ->columns(3)
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('permission_minutes')
                            ->label('Permission Minutes (Grace Period)')
                            ->placeholder('Enter minutes allowed before deduction')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1440)
                            ->default(0)
                            ->suffix('minutes')
                            ->helperText('Number of minutes allowed before calculating attendance deductions (0 = no grace period, max 1440 = 24 hours)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Assign Employees')
                    ->schema([
                        Forms\Components\Select::make('employees')
                            ->label('Select Employees')
                            ->multiple()
                            ->relationship('employees', 'name', fn($query) => $query->active())
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ]),
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
            'index' => Pages\ListWorkPlans::route('/'),
            'create' => Pages\CreateWorkPlan::route('/create'),
            'view' => Pages\ViewWorkPlan::route('/{record}'),
            'edit' => Pages\EditWorkPlan::route('/{record}/edit'),
        ];
    }
}
