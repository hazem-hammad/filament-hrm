<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobApplicationResource\Pages;
use App\Filament\Resources\JobApplicationResource\RelationManagers;
use App\Models\JobApplication;
use Carbon\Carbon;
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
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('https://linkedin.com/in/username'),
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
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('job.title')
                    ->label('Job Position')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Applicant')
                    ->getStateUsing(fn($record) => $record->first_name . ' ' . $record->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('years_of_experience')
                    ->label('Experience')
                    ->suffix(' yrs')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jobStage.name')
                    ->label('Stage')
                    ->badge()
                    ->color('success'),
                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('job_id')
                    ->label('Job Position')
                    ->relationship('job', 'title')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('job_stage_id')
                    ->label('Stage')
                    ->relationship('jobStage', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
                    ])
                    ->columns(1)
                    ->collapsible(),

                // Resume & Files
                Infolists\Components\Section::make('Resume & Documents')
                    ->schema(function (JobApplication $record) {
                        $schema = [];

                        // Display resume if exists
                        $resumeMedia = $record->getFirstMedia('resume');
                        if ($resumeMedia) {
                            $schema[] = Infolists\Components\TextEntry::make('resume')
                                ->label('Resume/CV')
                                ->state(function () use ($resumeMedia) {
                                    return '<a href="' . $resumeMedia->getUrl() . '" target="_blank" class="text-primary-600 hover:text-primary-500 font-medium flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                        </svg>
                                        ' . $resumeMedia->name . ' (' . $resumeMedia->human_readable_size . ')
                                    </a>';
                                })
                                ->html();
                        } else {
                            $schema[] = Infolists\Components\TextEntry::make('resume')
                                ->label('Resume/CV')
                                ->state('No resume uploaded')
                                ->color('gray');
                        }

                        return $schema;
                    })
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
                            if ($question->type === 'file_upload') {
                                // For file uploads, the answer contains the media ID
                                $mediaId = $value;
                                $media = $record->getMedia('custom_questions')->where('id', $mediaId)->first();

                                if ($media) {
                                    $fields[] = Infolists\Components\TextEntry::make("answer_{$answer->id}")
                                        ->label($question->title)
                                        ->state(function () use ($media) {
                                            return '<a href="' . $media->getUrl() . '" target="_blank" class="text-primary-600 hover:text-primary-500 font-medium flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                ' . $media->name . ' (' . $media->human_readable_size . ')
                                            </a>';
                                        })
                                        ->html();
                                } else {
                                    $fields[] = Infolists\Components\TextEntry::make("answer_{$answer->id}")
                                        ->label($question->title)
                                        ->state('File not found')
                                        ->color('danger');
                                }
                            } elseif ($question->type === 'multi_select') {
                                $decoded = json_decode($value, true);
                                $value = is_array($decoded) ? implode(', ', $decoded) : $value;
                                $fields[] = Infolists\Components\TextEntry::make("answer_{$answer->id}")
                                    ->label($question->title)
                                    ->state($value)
                                    ->badge();
                            } elseif ($question->type === 'toggle') {
                                $value = $value ? 'Yes' : 'No';
                                $fields[] = Infolists\Components\TextEntry::make("answer_{$answer->id}")
                                    ->label($question->title)
                                    ->state($value)
                                    ->badge()
                                    ->color($value === 'Yes' ? 'success' : 'gray');
                            } elseif ($question->type === 'date') {
                                $fields[] = Infolists\Components\TextEntry::make("answer_{$answer->id}")
                                    ->label($question->title)
                                    ->state($value ? Carbon::parse($value)->format('M d, Y') : 'Not provided');
                            } else {
                                // Text field, textarea, and other types
                                $fields[] = Infolists\Components\TextEntry::make("answer_{$answer->id}")
                                    ->label($question->title)
                                    ->state($value ?: 'Not provided')
                                    ->html($question->type === 'textarea');
                            }
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
