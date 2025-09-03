<?php

namespace App\Filament\Resources;

use App\Enum\InsuranceRelation;
use App\Enum\InsuranceStatus;
use App\Filament\Resources\MedicalRecordResource\Pages;
use App\Filament\Resources\MedicalRecordResource\RelationManagers;
use App\Models\Employee;
use App\Models\MedicalRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MedicalRecordResource extends Resource
{
    protected static ?string $model = MedicalRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    
    protected static ?string $navigationGroup = 'Employees';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        // Employee Selection Section
                        Forms\Components\Section::make('Employee Information')
                            ->description('Select the employee for this medical record')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Select::make('employee_id')
                                    ->label('Employee')
                                    ->placeholder('Select an employee')
                                    ->relationship('employee', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(fn() => request()->get('employee_id'))
                                    ->disabled(fn() => request()->has('employee_id'))
                                    ->helperText('Choose the employee this medical record belongs to'),
                            ])
                            ->columnSpan(['lg' => 1]),
                            
                        // Insurance Details Section
                        Forms\Components\Section::make('Insurance Details')
                            ->description('Medical insurance status and identification information')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Select::make('insurance_status')
                                    ->label('Insurance Status')
                                    ->placeholder('Select insurance status')
                                    ->options(InsuranceStatus::options())
                                    ->default(InsuranceStatus::NOT_APPLICABLE->value)
                                    ->required()
                                    ->helperText('Current status of the medical insurance')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state === InsuranceStatus::NOT_APPLICABLE->value) {
                                            $set('insurance_number', null);
                                            $set('insurance_relation', null);
                                        }
                                    }),
                                    
                                Forms\Components\TextInput::make('insurance_number')
                                    ->label('Insurance Number')
                                    ->placeholder('Enter insurance policy number')
                                    ->maxLength(255)
                                    ->helperText('Policy or membership number from the insurance provider')
                                    ->visible(fn($get) => $get('insurance_status') !== InsuranceStatus::NOT_APPLICABLE->value),
                                    
                                Forms\Components\Select::make('insurance_relation')
                                    ->label('Insurance Relation')
                                    ->placeholder('Select relationship type')
                                    ->options(InsuranceRelation::options())
                                    ->helperText('Relationship of the covered person to the primary policy holder')
                                    ->visible(fn($get) => $get('insurance_status') !== InsuranceStatus::NOT_APPLICABLE->value),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(['lg' => 2]),
                    
                // Cost Information Section
                Forms\Components\Section::make('Cost Information')
                    ->description('Insurance premium and cost details')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('annual_cost')
                                    ->label('Annual Cost')
                                    ->placeholder('0.00')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->maxValue(999999.99)
                                    ->helperText('Total yearly premium cost')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $set('monthly_cost', round($state / 12, 2));
                                        }
                                    }),
                                    
                                Forms\Components\TextInput::make('monthly_cost')
                                    ->label('Monthly Cost')
                                    ->placeholder('0.00')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->maxValue(99999.99)
                                    ->helperText('Monthly premium amount (auto-calculated from annual cost)')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $set('annual_cost', round($state * 12, 2));
                                        }
                                    }),
                            ])
                            ->columns(['lg' => 2]),
                    ])
                    ->collapsible()
                    ->visible(fn($get) => $get('insurance_status') !== InsuranceStatus::NOT_APPLICABLE->value),
                    
                // Timeline Section
                Forms\Components\Section::make('Coverage Timeline')
                    ->description('Insurance activation and deactivation dates')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\DatePicker::make('activation_date')
                                    ->label('Activation Date')
                                    ->placeholder('Select activation date')
                                    ->helperText('Date when the insurance coverage becomes active')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $deactivationDate = $get('deactivation_date');
                                        if ($state && $deactivationDate && $state >= $deactivationDate) {
                                            $set('deactivation_date', null);
                                        }
                                    }),
                                    
                                Forms\Components\DatePicker::make('deactivation_date')
                                    ->label('Deactivation Date')
                                    ->placeholder('Select deactivation date (optional)')
                                    ->helperText('Date when the insurance coverage ends (leave empty if still active)')
                                    ->after('activation_date')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $get) {
                                        $activationDate = $get('activation_date');
                                        if ($state && $activationDate && $state <= $activationDate) {
                                            return 'Deactivation date must be after activation date';
                                        }
                                    }),
                            ])
                            ->columns(['lg' => 2]),
                    ])
                    ->collapsible()
                    ->visible(fn($get) => $get('insurance_status') === InsuranceStatus::DONE->value),
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
            'index' => Pages\ListMedicalRecords::route('/'),
            'create' => Pages\CreateMedicalRecord::route('/create'),
            'edit' => Pages\EditMedicalRecord::route('/{record}/edit'),
        ];
    }
}
