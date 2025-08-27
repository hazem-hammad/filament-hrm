<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobApplicationResource\Pages;
use App\Filament\Resources\JobApplicationResource\RelationManagers;
use App\Models\JobApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Recruitment';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                ])
                    ->schema([
                        // Job Selection Section
                        Forms\Components\Section::make('Job Application')
                            ->schema([
                                Forms\Components\Select::make('job_id')
                                    ->label('Job Position')
                                    ->relationship('job', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('job_stage_id')
                                    ->label('Application Stage')
                                    ->relationship('jobStage', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(function () {
                                        return \App\Models\JobStage::query()
                                            ->active()
                                            ->orderBy('sort')
                                            ->first()?->id;
                                    }),
                            ])
                            ->columns(1),

                        // Personal Information Section
                        Forms\Components\Section::make('Personal Information')
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('years_of_experience')
                                    ->label('Years of Experience')
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->minValue(0),
                                Forms\Components\Toggle::make('status')
                                    ->label('Active')
                                    ->default(true)
                                    ->required(),
                            ])
                            ->columns(2),

                        // Optional URLs Section
                        Forms\Components\Section::make('Professional Links (Optional)')
                            ->schema([
                                Forms\Components\TextInput::make('linkedin_url')
                                    ->label('LinkedIn URL')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('https://linkedin.com/in/username'),
                                Forms\Components\TextInput::make('portfolio_url')
                                    ->label('Portfolio URL')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('https://yourportfolio.com'),
                                Forms\Components\TextInput::make('github_url')
                                    ->label('GitHub URL')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('https://github.com/username'),
                            ])
                            ->columns(1)
                            ->collapsible(),
                    ])
                    ->columnSpanFull(),

                // Dynamic Custom Questions Section
                Forms\Components\Section::make('Application Questions')
                    ->schema(function (Forms\Get $get) {
                        $jobId = $get('job_id');
                        if (!$jobId) {
                            return [
                                Forms\Components\Placeholder::make('select_job_first')
                                    ->label('')
                                    ->content('Please select a job position first to see custom questions.')
                                    ->columnSpanFull(),
                            ];
                        }

                        $customQuestions = \App\Models\Job::find($jobId)?->customQuestions ?? collect();

                        if ($customQuestions->isEmpty()) {
                            return [
                                Forms\Components\Placeholder::make('no_questions')
                                    ->label('')
                                    ->content('No custom questions for this job position.')
                                    ->columnSpanFull(),
                            ];
                        }

                        $fields = [];
                        foreach ($customQuestions as $question) {
                            $field = match ($question->type) {
                                'text_field' => Forms\Components\TextInput::make("question_{$question->id}")
                                    ->label($question->title)
                                    ->maxLength(500),
                                'date' => Forms\Components\DatePicker::make("question_{$question->id}")
                                    ->label($question->title),
                                'textarea' => Forms\Components\Textarea::make("question_{$question->id}")
                                    ->label($question->title)
                                    ->rows(4)
                                    ->maxLength(1000),
                                'file_upload' => Forms\Components\FileUpload::make("question_{$question->id}")
                                    ->label($question->title)
                                    ->disk('public')
                                    ->directory('application-files'),
                                'toggle' => Forms\Components\Toggle::make("question_{$question->id}")
                                    ->label($question->title),
                                'multi_select' => Forms\Components\Select::make("question_{$question->id}")
                                    ->label($question->title)
                                    ->multiple()
                                    ->options($question->options ? array_combine($question->options, $question->options) : [])
                                    ->searchable(),
                                default => Forms\Components\TextInput::make("question_{$question->id}")
                                    ->label($question->title)
                            };

                            if ($question->is_required) {
                                $field->required();
                            }

                            $fields[] = $field;
                        }

                        return $fields;
                    })
                    ->columnSpanFull()
                    ->visible(fn(Forms\Get $get): bool => !empty($get('job_id')))
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
                // Application Overview
                Infolists\Components\Section::make('Application Overview')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('job.title')
                                    ->label('Job Position')
                                    ->badge()
                                    ->color('primary')
                                    ->size('lg'),
                                Infolists\Components\TextEntry::make('jobStage.name')
                                    ->label('Current Stage')
                                    ->badge()
                                    ->color('success'),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Applied Date')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('status')
                                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                                    ->badge()
                                    ->color(fn(bool $state): string => $state ? 'success' : 'danger'),
                            ])
                    ]),

                // Applicant Information
                Infolists\Components\Section::make('Applicant Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('first_name')
                                    ->label('First Name'),
                                Infolists\Components\TextEntry::make('last_name')
                                    ->label('Last Name'),
                                Infolists\Components\TextEntry::make('email')
                                    ->copyable()
                                    ->icon('heroicon-o-envelope'),
                                Infolists\Components\TextEntry::make('phone')
                                    ->copyable()
                                    ->icon('heroicon-o-phone'),
                                Infolists\Components\TextEntry::make('years_of_experience')
                                    ->label('Experience')
                                    ->suffix(' years')
                                    ->numeric(),
                            ])
                    ]),

                // Professional Links
                Infolists\Components\Section::make('Professional Links')
                    ->schema([
                        Infolists\Components\TextEntry::make('linkedin_url')
                            ->label('LinkedIn')
                            ->url(true)
                            ->openUrlInNewTab()
                            ->placeholder('Not provided')
                            ->icon('heroicon-o-link'),
                        Infolists\Components\TextEntry::make('portfolio_url')
                            ->label('Portfolio')
                            ->url(true)
                            ->openUrlInNewTab()
                            ->placeholder('Not provided')
                            ->icon('heroicon-o-link'),
                        Infolists\Components\TextEntry::make('github_url')
                            ->label('GitHub')
                            ->url(true)
                            ->openUrlInNewTab()
                            ->placeholder('Not provided')
                            ->icon('heroicon-o-link'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                // Custom Question Answers
                Infolists\Components\Section::make('Application Answers')
                    ->schema(function (JobApplication $record) {
                        $answers = $record->answers()->with('customQuestion')->get();

                        if ($answers->isEmpty()) {
                            return [
                                Infolists\Components\TextEntry::make('no_answers')
                                    ->label('')
                                    ->state('No custom questions were answered for this application.')
                                    ->columnSpanFull(),
                            ];
                        }

                        $fields = [];
                        foreach ($answers as $answer) {
                            $question = $answer->customQuestion;
                            $value = $answer->answer;

                            // Handle different question types
                            if ($question->type === 'multi_select') {
                                $decoded = json_decode($value, true);
                                $value = is_array($decoded) ? implode(', ', $decoded) : $value;
                            } elseif ($question->type === 'toggle') {
                                $value = $value ? 'Yes' : 'No';
                            }

                            $fields[] = Infolists\Components\TextEntry::make("answer_{$answer->id}")
                                ->label($question->title)
                                ->state($value)
                                ->badge($question->type === 'multi_select')
                                ->html($question->type === 'textarea');
                        }

                        return $fields;
                    })
                    ->columns(1)
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
            'index' => Pages\ListJobApplications::route('/'),
            'create' => Pages\CreateJobApplication::route('/create'),
            'view' => Pages\ViewJobApplication::route('/{record}'),
            'edit' => Pages\EditJobApplication::route('/{record}/edit'),
        ];
    }
}
