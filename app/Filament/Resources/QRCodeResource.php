<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QRCodeResource\Pages;
use App\Filament\Resources\QRCodeResource\RelationManagers;
use App\Models\QRCode;
use App\Services\QRCodeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;

class QRCodeResource extends Resource
{
    protected static ?string $model = QRCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationGroup = 'Tools';

    protected static ?string $label = 'QR Code';

    protected static ?string $pluralLabel = 'QR Codes';

    public static function form(Form $form): Form
    {
        $qrService = app(QRCodeService::class);

        return $form
            ->schema([
                Forms\Components\Section::make('Contact Information')
                    ->description('Enter the contact details that will be encoded in the QR code.')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Primary Phone')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('+1234567890'),
                        Forms\Components\TextInput::make('phone_2')
                            ->label('Secondary Phone')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('+1234567890'),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('website')
                            ->label('Website URL')
                            ->url()
                            ->prefix('https://')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Design Customization')
                    ->description('Customize the appearance and style of your QR code.')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('size')
                                ->label('Size (pixels)')
                                ->numeric()
                                ->default(300)
                                ->minValue(100)
                                ->maxValue(1000)
                                ->suffix('px')
                                ->helperText('Recommended: 300-500px'),

                            Forms\Components\TextInput::make('margin')
                                ->label('Margin')
                                ->numeric()
                                ->default(1)
                                ->minValue(0)
                                ->maxValue(10)
                                ->helperText('Border spacing around QR code'),
                        ]),
                    ]),

                Forms\Components\Section::make('Color Customization')
                    ->description('Personalize your QR code with custom colors and gradients.')
                    ->icon('heroicon-o-swatch')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\ColorPicker::make('background_color')
                                ->label('Background Color')
                                ->default('#FFFFFF')
                                ->helperText('Base background color'),

                            Forms\Components\ColorPicker::make('foreground_color')
                                ->label('Foreground Color')
                                ->default('#000000')
                                ->helperText('QR code pattern color'),
                        ]),

                    ]),

                Forms\Components\Section::make('QR Code Preview & Settings')
                    ->description('Preview your customized QR code and manage settings.')
                    ->icon('heroicon-o-eye')
                    ->schema([

                        Forms\Components\View::make('filament.qr-preview')
                            ->label('QR Code Preview')
                            ->viewData(function (?QRCode $record) {
                                if (!$record || !$record->qr_code_path) {
                                    return [
                                        'hasQrCode' => false,
                                        'qrUrl' => null,
                                    ];
                                }
                                $qrService = app(QRCodeService::class);
                                return [
                                    'hasQrCode' => true,
                                    'qrUrl' => $qrService->getQRCodeUrl($record),
                                ];
                            })
                            ->visible(fn(?QRCode $record) => $record && $record->exists)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('encoding')
                            ->label('Character Encoding')
                            ->default('UTF-8')
                            ->helperText('Character encoding for the QR code data')
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable()
                    ->weight(FontWeight::Medium)
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Primary Phone')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone copied!')
                    ->icon('heroicon-m-phone'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->icon('heroicon-m-envelope'),
                Tables\Columns\ImageColumn::make('qr_code_path')
                    ->label('QR Code')
                    ->disk('public')
                    ->size(60)
                    ->visibility('public'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('download')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary')
                        ->action(function (QRCode $record) {
                            $qrService = app(QRCodeService::class);
                            return $qrService->downloadQRCode($record);
                        })
                        ->visible(fn(QRCode $record) => $record->qr_code_path !== null),
                    Tables\Actions\Action::make('regenerate')
                        ->label('Regenerate QR')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Regenerate QR Code')
                        ->modalDescription('This will generate a new QR code. The old QR code will be replaced.')
                        ->action(function (QRCode $record) {
                            $qrService = app(QRCodeService::class);
                            $qrService->regenerateQRCode($record);
                        }),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn($records) => $records->each(fn($record) => $record->update(['is_active' => true]))),
                ]),
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
            'index' => Pages\ListQRCodes::route('/'),
            'create' => Pages\CreateQRCode::route('/create'),
            'edit' => Pages\EditQRCode::route('/{record}/edit'),
        ];
    }
}
