<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomQuestionResource\Pages;
use App\Filament\Resources\CustomQuestionResource\RelationManagers;
use App\Models\CustomQuestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomQuestionResource extends Resource
{
    protected static ?string $model = CustomQuestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Recruitment';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Custom Question Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'text_field' => 'Text Field',
                                'date' => 'Date',
                                'textarea' => 'Textarea',
                                'file_upload' => 'File Upload',
                                'toggle' => 'Toggle',
                                'multi_select' => 'Multi Select',
                            ])
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => 
                                $state !== 'multi_select' ? $set('options', null) : null
                            ),
                        Forms\Components\Toggle::make('is_required')
                            ->label('Required')
                            ->default(false),
                        Forms\Components\Toggle::make('status')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                        Forms\Components\Repeater::make('options')
                            ->label('Options (for Multi Select)')
                            ->simple(
                                Forms\Components\TextInput::make('option')
                                    ->required()
                            )
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'multi_select')
                            ->columnSpanFull()
                            ->minItems(1)
                            ->maxItems(10),
                    ])
                    ->columns(3),
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
            'index' => Pages\ListCustomQuestions::route('/'),
            'create' => Pages\CreateCustomQuestion::route('/create'),
            'edit' => Pages\EditCustomQuestion::route('/{record}/edit'),
        ];
    }
}
