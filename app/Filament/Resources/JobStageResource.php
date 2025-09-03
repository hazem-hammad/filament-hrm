<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobStageResource\Pages;
use App\Models\JobStage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class JobStageResource extends Resource
{
    protected static ?string $model = JobStage::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'Recruitment';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Job Stage Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sort')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Forms\Components\Toggle::make('status')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Email Notifications')
                    ->schema([
                        Forms\Components\Toggle::make('sending_email')
                            ->label('Send Email Notifications')
                            ->helperText('When enabled, applicants will receive email notifications when they reach this stage.')
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('email_template')
                            ->label('Email Template')
                            ->helperText('Email template sent to applicants when they reach this stage. Use {first_name}, {last_name}, {job_title}, and {stage_name} as placeholders.')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                                'h2',
                                'h3',
                                'blockquote',
                            ])
                            ->visible(fn(Forms\Get $get): bool => $get('sending_email'))
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
            'index' => Pages\ListJobStages::route('/'),
        ];
    }
}
