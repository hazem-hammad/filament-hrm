<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Models\NotificationCenter;
use App\Services\ModuleService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationResource extends Resource
{
    protected static ?string $model = NotificationCenter::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    
    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    public static function getModelLabel(): string
    {
        return __('Notification');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Notifications');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema(function () {
                    $localesConfig = getLocalesConfig();

                    return array_merge(
                        // Dynamic title inputs based on available locales
                        collect($localesConfig['locales'])
                            ->map(
                                fn($locale) => Forms\Components\TextInput::make("title.{$locale}")
                                    ->label(__('Title') . ($localesConfig['isSingle'] ? '' : " (" . strtoupper($locale) . ")"))
                                    ->required()
                                    ->maxLength(40)
                                    ->columnSpan($localesConfig['columnSpan'])
                            )->toArray(),

                        // Dynamic body inputs based on available locales
                        collect($localesConfig['locales'])
                            ->map(
                                fn($locale) => Forms\Components\TextInput::make("body.{$locale}")
                                    ->label(__('Body') . ($localesConfig['isSingle'] ? '' : " (" . strtoupper($locale) . ")"))
                                    ->required()
                                    ->maxLength(300)
                                    ->columnSpan($localesConfig['columnSpan'])
                            )->toArray(),

                        // Static fields
                    );
                })->columns(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ...collect(config('core.available_locales', ['en']))
                    ->map(
                        fn($locale, $index) => Tables\Columns\TextColumn::make("title_{$locale}")
                            ->label(__('Title') . (count(config('core.available_locales', ['en'])) === 1 ? '' : " (" . strtoupper($locale) . ")"))
                            ->getStateUsing(fn($record) => $record->getTranslation('title', $locale))
                            ->limit(30)
                            ->searchable()
                            ->sortable($index === 0)
                    )->toArray(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime()
                    ->sortable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
        ];
    }
}
