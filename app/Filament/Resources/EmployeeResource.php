<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use App\Models\Position;
use App\Models\DocumentType;
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
                                    ->helperText('Please use with country code. (ex. +91)'),
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
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->placeholder('Enter employee email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->placeholder('Enter employee password')
                                    ->password()
                                    ->required(fn(string $operation): bool => $operation === 'create')
                                    ->maxLength(255)
                                    ->revealable()
                                    ->dehydrated(fn($state) => filled($state))
                                    ->helperText('Leave empty to keep current password (edit only)'),
                                Forms\Components\Textarea::make('address')
                                    ->label('Address')
                                    ->placeholder('Enter employee address')
                                    ->columnSpanFull()
                                    ->rows(4),
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
                                Forms\Components\Select::make('reporting_to')
                                    ->label('Reports To (Manager)')
                                    ->placeholder('Select Direct Manager')
                                    ->relationship('manager', 'name', fn($query, $livewire) => $query
                                        ->where('status', true)
                                        ->when($livewire->record ?? null, fn($q) => $q->where('id', '!=', $livewire->record->id))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Select the direct manager this employee reports to')
                                    ->columnSpanFull(),
                                Forms\Components\DatePicker::make('company_date_of_joining')
                                    ->label('Company Date Of Joining')
                                    ->required()
                                    ->default(today())
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),

                // Documents Section
                Forms\Components\Section::make('Document')
                    ->schema(static::getDocumentFields())
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    protected static function getDocumentFields(): array
    {
        $fields = [];

        // Get all document types
        $documentTypes = DocumentType::query()->active()->get();

        foreach ($documentTypes as $documentType) {
            $field = SpatieMediaLibraryFileUpload::make($documentType->name)
                ->label($documentType->name)
                ->collection($documentType->name)
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'])
                ->maxSize(5120)
                ->disk('public')
                ->directory('employee-documents')
                ->visibility('private')
                ->downloadable()
                ->openable()
                ->previewable()
                ->reorderable()
                ->columnSpan(1);

            if ($documentType->is_required) {
                $field->required()->maxFiles(1);
            } else {
                $field->maxFiles(3);
            }

            $fields[] = $field;
        }

        return $fields;
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
