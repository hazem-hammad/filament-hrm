<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function afterSave(): void
    {
        $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->getRecord()]));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Setting Details'))->schema([

                    TextInput::make('name')
                        ->disabled()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Hidden::make('type'),

                    // text column
                    TextInput::make('value')
                        ->visible(fn(Get $get) => $get('type') === 'text')
                        ->label(__('Value'))
                        ->columnSpanFull(),

                    ColorPicker::make('value')
                        ->visible(fn(Get $get) => $get('type') === 'color')
                        ->label(__('Value'))
                        ->columnSpanFull(),

                    // status dropdown
                    Forms\Components\Select::make('value')
                        ->visible(fn(Get $get) => $get('type') === 'boolean')
                        ->label(__('Value'))
                        ->options([
                            '1' => __('Yes'),
                            '0' => __('No'),
                        ])
                        ->columnSpanFull(),

                    // media column
                    Forms\Components\SpatieMediaLibraryFileUpload::make('media')
                        ->visible(fn(Get $get) => $get('type') === 'file')
                        ->collection(fn(Get $get) => $get('media_collection_name') ?? 'default')
                        ->directory('settings')
                        ->image()
                        ->imageEditor()
                        ->acceptedFileTypes(['image/*'])
                        ->maxSize(5120)
                        ->required()
                        ->columnSpanFull()
                        ->label(__('File Upload')),

                ])->columns(2),
            ]);
    }
}
