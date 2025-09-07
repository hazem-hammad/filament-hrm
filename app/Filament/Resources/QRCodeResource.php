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

class QRCodeResource extends Resource
{
    protected static ?string $model = QRCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    
    protected static ?string $navigationGroup = 'Tools';
    
    protected static ?string $label = 'QR Code';
    
    protected static ?string $pluralLabel = 'QR Codes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contact Information')
                    ->description('Enter the contact details that will be encoded in the QR code.')
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
                
                Forms\Components\Section::make('QR Code Settings')
                    ->description('QR code generation and display settings.')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('When disabled, the QR code will not be publicly accessible.'),
                        Forms\Components\Placeholder::make('qr_preview')
                            ->label('QR Code Preview')
                            ->content(function (?QRCode $record) {
                                if (!$record || !$record->qr_code_path) {
                                    return 'QR Code will be generated after saving.';
                                }
                                $qrService = app(QRCodeService::class);
                                $url = $qrService->getQRCodeUrl($record);
                                return $url ? "<img src='{$url}' alt='QR Code' style='max-width: 200px;'>" : 'QR Code not found.';
                            })
                            ->visible(fn (?QRCode $record) => $record && $record->exists),
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
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
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
                        ->visible(fn (QRCode $record) => $record->qr_code_path !== null),
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
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn (QRCode $record) => $record->is_active ? 'Deactivate' : 'Activate')
                        ->icon(fn (QRCode $record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                        ->color(fn (QRCode $record) => $record->is_active ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (QRCode $record) => ($record->is_active ? 'Deactivate' : 'Activate') . ' QR Code')
                        ->modalDescription(fn (QRCode $record) => 'Are you sure you want to ' . ($record->is_active ? 'deactivate' : 'activate') . ' this QR code?')
                        ->action(fn (QRCode $record) => $record->update(['is_active' => !$record->is_active])),
                    Tables\Actions\EditAction::make(),
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
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => true]))),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => false]))),
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
