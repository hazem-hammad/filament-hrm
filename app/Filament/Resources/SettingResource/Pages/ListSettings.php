<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action since we disabled creation
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Setting::query()->where('is_configurable_by_admin', true))
            ->paginationPageOptions([50])
            ->defaultGroup('group')
            ->groups([
                Group::make('group')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            ])
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('value')
                    ->label(__('Value'))
                    ->limit(50)
                    ->wrap()
                    ->searchable(),

                SpatieMediaLibraryImageColumn::make('preview')
                    ->collection('default')
                    ->label(__('Preview'))
                    ->size(40)
                    ->getStateUsing(function ($record) {
                        return $record->type === 'file' && $record->media_collection_name ? $record : null;
                    }),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }
}
