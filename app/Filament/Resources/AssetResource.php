<?php

namespace App\Filament\Resources;

use App\Enum\AssetCondition;
use App\Enum\AssetStatus;
use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $navigationGroup = 'Asset Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                ])
                    ->schema([
                        // Asset Information Section
                        Forms\Components\Section::make('Asset Information')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('images')
                                    ->label('Asset Image')
                                    ->collection('images')
                                    ->image()
                                    ->imageEditor()
                                    ->imageResizeTargetWidth(800)
                                    ->imageResizeTargetHeight(600)
                                    ->columnSpanFull()
                                    ->helperText('Upload an image of the asset'),

                                Forms\Components\TextInput::make('asset_id')
                                    ->label('Asset ID')
                                    ->default(fn() => Asset::generateAssetId())
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('name')
                                    ->label('Asset Name')
                                    ->placeholder('Enter asset name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('category')
                                    ->label('Category')
                                    ->placeholder('Select category')
                                    ->options([
                                        'Computer Equipment' => 'Computer Equipment',
                                        'Office Equipment' => 'Office Equipment',
                                        'Furniture' => 'Furniture',
                                        'Vehicle' => 'Vehicle',
                                        'Software' => 'Software',
                                        'Mobile Device' => 'Mobile Device',
                                        'Network Equipment' => 'Network Equipment',
                                        'Other' => 'Other',
                                    ])
                                    ->searchable()
                                    ->required(),

                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->placeholder('Enter asset description')
                                    ->columnSpanFull()
                                    ->rows(3),

                                Forms\Components\TextInput::make('brand')
                                    ->label('Brand')
                                    ->placeholder('Enter brand name')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('model')
                                    ->label('Model')
                                    ->placeholder('Enter model number')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('serial_number')
                                    ->label('Serial Number')
                                    ->placeholder('Enter serial number')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                            ])
                            ->columns(2),

                        // Purchase Information Section
                        Forms\Components\Section::make('Purchase Information')
                            ->schema([
                                Forms\Components\TextInput::make('purchase_cost')
                                    ->label('Purchase Cost')
                                    ->placeholder('0.00')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step('0.01'),

                                Forms\Components\DatePicker::make('purchase_date')
                                    ->label('Purchase Date')
                                    ->native(false)
                                    ->default(today()),

                                Forms\Components\TextInput::make('warranty_months')
                                    ->label('Warranty Period (Months)')
                                    ->placeholder('Enter warranty period in months')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(120)
                                    ->helperText('Warranty expiration date will be calculated automatically'),

                                Forms\Components\DatePicker::make('warranty_expires_at')
                                    ->label('Warranty Expires At')
                                    ->native(false)
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->columns(2),

                        // Status Information Section
                        Forms\Components\Section::make('Status Information')
                            ->schema([
                                Forms\Components\Select::make('condition')
                                    ->label('Condition')
                                    ->placeholder('Select condition')
                                    ->options(AssetCondition::options())
                                    ->default(AssetCondition::GOOD->value)
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->placeholder('Select status')
                                    ->options(AssetStatus::options())
                                    ->default(AssetStatus::AVAILABLE->value)
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state !== AssetStatus::ASSIGNED->value) {
                                            $set('assigned_to', null);
                                            $set('assigned_at', null);
                                        }
                                    }),

                                Forms\Components\TextInput::make('location')
                                    ->label('Location')
                                    ->placeholder('Enter asset location')
                                    ->maxLength(255),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Inactive assets will not be available for assignment'),
                            ])
                            ->columns(2),

                        // Assignment Information Section
                        Forms\Components\Section::make('Assignment Information')
                            ->schema([
                                Forms\Components\Select::make('assigned_to')
                                    ->label('Assigned To')
                                    ->placeholder('Select employee')
                                    ->relationship('assignedEmployee', 'name', fn($query) => $query->active())
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn(Forms\Get $get) => $get('status') === AssetStatus::ASSIGNED->value)
                                    ->live(),

                                Forms\Components\DatePicker::make('assigned_at')
                                    ->label('Assigned At')
                                    ->native(false)
                                    ->default(today())
                                    ->visible(fn(Forms\Get $get) => $get('assigned_to') !== null),
                            ])
                            ->visible(fn(Forms\Get $get) => $get('status') === AssetStatus::ASSIGNED->value)
                            ->columns(2),

                        // Additional Information Section
                        Forms\Components\Section::make('Additional Information')
                            ->schema([
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notes')
                                    ->placeholder('Enter any additional notes')
                                    ->columnSpanFull()
                                    ->rows(4),

                                SpatieMediaLibraryFileUpload::make('documents')
                                    ->label('Documents')
                                    ->collection('documents')
                                    ->multiple()
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->columnSpanFull()
                                    ->helperText('Upload receipts, warranties, manuals, or other related documents'),
                            ]),
                    ])
                    ->columnSpanFull(),
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
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
