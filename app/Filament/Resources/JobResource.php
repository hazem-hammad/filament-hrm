<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Models\Job;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Recruitment';

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
                        // Basic Job Information Section
                        Forms\Components\Section::make('Basic Job Information')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('department_id')
                                    ->label('Department')
                                    ->relationship('department', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('position_id', null);
                                    }),
                                Forms\Components\Select::make('position_id')
                                    ->label('Position')
                                    ->options(fn (Forms\Get $get): Collection => 
                                        \App\Models\Position::query()
                                            ->where('department_id', $get('department_id'))
                                            ->where('status', true)
                                            ->pluck('name', 'id')
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('number_of_positions')
                                    ->label('Number of Positions')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1),
                            ])
                            ->columns(2),

                        // Job Details Section
                        Forms\Components\Section::make('Job Details')
                            ->schema([
                                Forms\Components\Select::make('work_type')
                                    ->label('Work Type')
                                    ->options([
                                        'full_time' => 'Full Time',
                                        'part_time' => 'Part Time',
                                        'contract' => 'Contract',
                                        'internship' => 'Internship',
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('work_mode')
                                    ->label('Work Mode')
                                    ->options([
                                        'remote' => 'Remote',
                                        'onsite' => 'Onsite',
                                        'hybrid' => 'Hybrid',
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('experience_level')
                                    ->label('Experience Level')
                                    ->options([
                                        'entry' => 'Entry',
                                        'junior' => 'Junior',
                                        'mid' => 'Mid',
                                        'senior' => 'Senior',
                                        'lead' => 'Lead',
                                    ])
                                    ->required(),
                                Forms\Components\Toggle::make('status')
                                    ->label('Active')
                                    ->default(true)
                                    ->required(),
                            ])
                            ->columns(2),

                        // Dates Section
                        Forms\Components\Section::make('Job Timeline')
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->required()
                                    ->default(today()),
                                Forms\Components\DatePicker::make('end_date')
                                    ->required()
                                    ->after('start_date'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),

                // Descriptions Section
                Forms\Components\Section::make('Job Descriptions')
                    ->schema([
                        Forms\Components\Textarea::make('short_description')
                            ->required()
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('long_description')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('job_requirements')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('benefits')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // Application Questions Section
                Forms\Components\Section::make('Application Questions')
                    ->schema([
                        Forms\Components\Select::make('customQuestions')
                            ->label('Custom Questions')
                            ->multiple()
                            ->relationship('customQuestions', 'title')
                            ->options(
                                \App\Models\CustomQuestion::query()
                                    ->active()
                                    ->orderBy('title')
                                    ->pluck('title', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->helperText('Select custom questions that will be asked to applicants for this job')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Job Overview Section
                Infolists\Components\Section::make('Job Overview')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('title')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->columnSpanFull(),
                                Infolists\Components\TextEntry::make('department.name')
                                    ->label('Department')
                                    ->badge()
                                    ->color('primary'),
                                Infolists\Components\TextEntry::make('position.name')
                                    ->label('Position')
                                    ->badge()
                                    ->color('success'),
                                Infolists\Components\TextEntry::make('number_of_positions')
                                    ->label('Number of Positions')
                                    ->numeric(),
                                Infolists\Components\TextEntry::make('status')
                                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                                    ->badge()
                                    ->color(fn(bool $state): string => $state ? 'success' : 'danger'),
                            ])
                    ]),

                // Job Details Section
                Infolists\Components\Section::make('Job Details')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('work_type')
                                    ->formatStateUsing(fn(string $state): string => 
                                        match($state) {
                                            'full_time' => 'Full Time',
                                            'part_time' => 'Part Time',
                                            'contract' => 'Contract',
                                            'internship' => 'Internship',
                                            default => $state
                                        }
                                    )
                                    ->badge()
                                    ->color('info'),
                                Infolists\Components\TextEntry::make('work_mode')
                                    ->formatStateUsing(fn(string $state): string => 
                                        match($state) {
                                            'remote' => 'Remote',
                                            'onsite' => 'Onsite',
                                            'hybrid' => 'Hybrid',
                                            default => $state
                                        }
                                    )
                                    ->badge()
                                    ->color('warning'),
                                Infolists\Components\TextEntry::make('experience_level')
                                    ->label('Experience Level')
                                    ->formatStateUsing(fn(string $state): string => 
                                        match($state) {
                                            'entry' => 'Entry Level',
                                            'junior' => 'Junior',
                                            'mid' => 'Mid Level',
                                            'senior' => 'Senior',
                                            'lead' => 'Lead',
                                            default => $state
                                        }
                                    )
                                    ->badge()
                                    ->color('gray'),
                            ])
                    ]),

                // Timeline Section
                Infolists\Components\Section::make('Timeline')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('start_date')
                                    ->date()
                                    ->icon('heroicon-o-calendar'),
                                Infolists\Components\TextEntry::make('end_date')
                                    ->date()
                                    ->icon('heroicon-o-calendar'),
                            ])
                    ]),

                // Job Description Section
                Infolists\Components\Section::make('Job Description')
                    ->schema([
                        Infolists\Components\TextEntry::make('short_description')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('long_description')
                            ->html()
                            ->columnSpanFull(),
                    ]),

                // Requirements and Benefits Section
                Infolists\Components\Section::make('Requirements & Benefits')
                    ->schema([
                        Infolists\Components\TextEntry::make('job_requirements')
                            ->label('Job Requirements')
                            ->html()
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('benefits')
                            ->html()
                            ->columnSpanFull()
                            ->placeholder('No benefits specified'),
                    ]),

                // Application Questions Section
                Infolists\Components\Section::make('Application Questions')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('customQuestions')
                            ->label('Custom Questions')
                            ->schema([
                                Infolists\Components\TextEntry::make('title')
                                    ->weight('bold'),
                                Infolists\Components\TextEntry::make('type')
                                    ->formatStateUsing(fn(string $state): string => 
                                        match($state) {
                                            'text_field' => 'Text Field',
                                            'date' => 'Date',
                                            'textarea' => 'Textarea',
                                            'file_upload' => 'File Upload',
                                            'toggle' => 'Toggle',
                                            'multi_select' => 'Multi Select',
                                            default => $state
                                        }
                                    )
                                    ->badge()
                                    ->color('info'),
                                Infolists\Components\TextEntry::make('is_required')
                                    ->label('Required')
                                    ->formatStateUsing(fn(bool $state): string => $state ? 'Yes' : 'No')
                                    ->badge()
                                    ->color(fn(bool $state): string => $state ? 'danger' : 'gray'),
                            ])
                            ->columns(3)
                            ->placeholder('No custom questions assigned to this job'),
                    ])
                    ->collapsible(),
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
            'index' => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'view' => Pages\ViewJob::route('/{record}'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
    }
}