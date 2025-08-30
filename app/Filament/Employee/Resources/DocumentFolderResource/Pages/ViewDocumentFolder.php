<?php

namespace App\Filament\Employee\Resources\DocumentFolderResource\Pages;

use App\Filament\Employee\Resources\DocumentFolderResource;
use App\Models\Document;
use App\Models\DocumentFolder;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Table;

class ViewDocumentFolder extends ViewRecord
{
    protected static string $resource = DocumentFolderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Folder Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Folder Name')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->icon('heroicon-o-folder')
                                    ->iconColor(fn($record): string => $record->color),

                                TextEntry::make('parent.name')
                                    ->label('Parent Folder')
                                    ->placeholder('Root Directory')
                                    ->icon('heroicon-o-folder-open'),

                                TextEntry::make('description')
                                    ->label('Description')
                                    ->placeholder('No description')
                                    ->columnSpanFull(),

                                TextEntry::make('creator.name')
                                    ->label('Created By')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('item_count')
                                    ->label('Total Items')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('formatted_size')
                                    ->label('Total Size')
                                    ->badge()
                                    ->color('success'),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

}