<?php

namespace App\Filament\Resources;

use App\Enum\EmployeeLevel;
use App\Enum\MaritalStatus;
use App\Enum\ContractType;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use App\Models\Position;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'Employees';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                ])
                    ->schema([
                        // Personal Detail Section
                        Forms\Components\Section::make('Personal Detail')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('profile')
                                    ->label('Profile Photo')
                                    ->collection('profile')
                                    ->image()
                                    ->imageEditor()
                                    ->imageCropAspectRatio('1:1')
                                    ->imageResizeTargetWidth(300)
                                    ->imageResizeTargetHeight(300)
                                    ->columnSpanFull()
                                    ->helperText('Upload a profile photo (recommended size: 300x300px)'),
                                Forms\Components\TextInput::make('name')
                                    ->label('Name')
                                    ->placeholder('Enter employee name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Phone')
                                    ->placeholder('Enter employee phone')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Please use with country code. (ex. +20)'),
                                Forms\Components\TextInput::make('business_phone')
                                    ->label('Business Phone')
                                    ->placeholder('Enter business phone')
                                    ->tel()
                                    ->maxLength(255)
                                    ->helperText('Optional business phone number'),
                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->label('Date of Birth')
                                    ->required()
                                    ->default(today()),
                                Forms\Components\Radio::make('gender')
                                    ->label('Gender')
                                    ->options([
                                        'male' => 'Male',
                                        'female' => 'Female',
                                    ])
                                    ->default('male')
                                    ->required()
                                    ->inline(),
                                Forms\Components\Radio::make('marital_status')
                                    ->label('Marital Status')
                                    ->options(MaritalStatus::options())
                                    ->default(MaritalStatus::SINGLE->value)
                                    ->required()
                                    ->inline(),
                                Forms\Components\TextInput::make('email')
                                    ->label('Work Email')
                                    ->placeholder('Enter employee work email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('A welcome email with login credentials will be sent after creation'),
                                Forms\Components\TextInput::make('personal_email')
                                    ->label('Personal Email')
                                    ->placeholder('Enter employee personal email')
                                    ->email()
                                    ->maxLength(255)
                                    ->helperText('Optional personal email address'),
                                Forms\Components\TextInput::make('national_id')
                                    ->label('National ID Number')
                                    ->placeholder('Enter national ID number')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->helperText('Required national identification number'),
                                Forms\Components\Textarea::make('address')
                                    ->label('Address')
                                    ->placeholder('Enter employee address')
                                    ->columnSpanFull()
                                    ->rows(4),
                            ])
                            ->columns(2),

                        // Emergency Contact Section
                        Forms\Components\Section::make('Emergency Contact')
                            ->description('Optional emergency contact information')
                            ->schema([
                                Forms\Components\TextInput::make('emergency_contact_name')
                                    ->label('Contact Name')
                                    ->placeholder('Enter emergency contact name')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('emergency_contact_relation')
                                    ->label('Relationship')
                                    ->placeholder('Enter relationship (e.g., Spouse, Parent, Sibling)')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('emergency_contact_phone')
                                    ->label('Contact Phone')
                                    ->placeholder('Enter emergency contact phone')
                                    ->tel()
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->helperText('Please use with country code. (ex. +20)'),
                            ])
                            ->columns(2),

                        // Company Detail Section
                        Forms\Components\Section::make('Company Detail')
                            ->schema([
                                Forms\Components\TextInput::make('employee_id')
                                    ->label('Employee ID')
                                    ->default(fn() => Employee::generateEmployeeId())
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled()
                                    ->columnSpanFull()
                                    ->dehydrated(),
                                Forms\Components\Select::make('department_id')
                                    ->label('Select Department')
                                    ->placeholder('Select Department')
                                    ->relationship('department', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Toggle::make('status')
                                            ->label('Active')
                                            ->default(true)
                                            ->required(),
                                    ])
                                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                        return $action
                                            ->modalHeading('Create Department')
                                            ->modalWidth('md');
                                    })
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('position_id', null);
                                    }),
                                Forms\Components\Select::make('position_id')
                                    ->label('Select Designation')
                                    ->placeholder('Select Designation')
                                    ->options(fn(Get $get): Collection => Position::query()
                                        ->where('department_id', $get('department_id'))
                                        ->where('status', true)
                                        ->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Select::make('department_id')
                                            ->label('Department')
                                            ->relationship('department', 'name')
                                            ->required(),
                                        Forms\Components\Toggle::make('status')
                                            ->label('Active')
                                            ->default(true)
                                            ->required(),
                                    ])
                                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                        return $action
                                            ->modalHeading('Create Position')
                                            ->modalWidth('md');
                                    })
                                    ->required(),
                                Forms\Components\Select::make('level')
                                    ->label('Employee Level')
                                    ->placeholder('Select Employee Level')
                                    ->options(EmployeeLevel::options())
                                    ->default(EmployeeLevel::ENTRY->value)
                                    ->required()
                                    ->helperText('Select the employee level/seniority'),
                                Forms\Components\Select::make('contract_type')
                                    ->label('Contract Type')
                                    ->placeholder('Select Contract Type')
                                    ->options(ContractType::options())
                                    ->default(ContractType::PERMANENT->value)
                                    ->required()
                                    ->helperText('Select the employment contract type'),
                                Forms\Components\Select::make('reporting_to')
                                    ->label('Reports To (Manager)')
                                    ->placeholder('Select Direct Manager')
                                    ->relationship(
                                        'manager',
                                        'name',
                                        fn($query, $livewire) => $query
                                            ->where('status', true)
                                            ->when($livewire->record ?? null, fn($q) => $q->where('id', '!=', $livewire->record->id))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Select the direct manager this employee reports to'),
                                Forms\Components\DatePicker::make('company_date_of_joining')
                                    ->label('Company Date Of Joining')
                                    ->required()
                                    ->default(today())
                                    ->columnSpanFull(),
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
            \App\Filament\Resources\EmployeeResource\RelationManagers\MedicalRecordsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
