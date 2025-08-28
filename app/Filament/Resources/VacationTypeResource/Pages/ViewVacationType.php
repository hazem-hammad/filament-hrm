<?php

namespace App\Filament\Resources\VacationTypeResource\Pages;

use App\Filament\Resources\VacationTypeResource;
use Filament\Actions;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewVacationType extends ViewRecord
{
    protected static string $resource = VacationTypeResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                ])
                    ->schema([
                        // Basic Information Section
                        Components\Section::make('Basic Information')
                            ->schema([
                                Components\TextEntry::make('name')
                                    ->label('Vacation Type Name')
                                    ->icon('heroicon-o-tag'),
                                Components\TextEntry::make('description')
                                    ->label('Description')
                                    ->placeholder('No description provided')
                                    ->columnSpanFull(),
                                Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive'),
                            ])
                            ->columns(2),

                        // Vacation Settings Section
                        Components\Section::make('Vacation Settings')
                            ->schema([
                                Components\TextEntry::make('balance')
                                    ->label('Annual Balance')
                                    ->suffix(' days')
                                    ->numeric()
                                    ->icon('heroicon-o-calendar'),
                                Components\TextEntry::make('unlock_after_months')
                                    ->label('Available After')
                                    ->suffix(' months')
                                    ->formatStateUsing(fn (int $state): string => 
                                        $state === 0 ? 'Immediately' : $state . ' months after joining'
                                    )
                                    ->color(fn (int $state): string => $state === 0 ? 'success' : 'warning')
                                    ->badge()
                                    ->icon('heroicon-o-clock'),
                                Components\TextEntry::make('required_days_before')
                                    ->label('Notice Period')
                                    ->suffix(' days')
                                    ->formatStateUsing(fn (int $state): string => 
                                        $state === 0 ? 'No notice required' : $state . ' days notice'
                                    )
                                    ->icon('heroicon-o-bell'),
                                Components\TextEntry::make('requires_approval')
                                    ->label('Approval Required')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'warning' : 'success')
                                    ->formatStateUsing(fn (bool $state): string => 
                                        $state ? 'Multi-level approval required' : 'No approval required'
                                    )
                                    ->icon('heroicon-o-check-circle'),
                            ])
                            ->columns(2),

                        // Timestamps Section
                        Components\Section::make('System Information')
                            ->schema([
                                Components\TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime()
                                    ->icon('heroicon-o-plus-circle'),
                                Components\TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime()
                                    ->icon('heroicon-o-pencil'),
                            ])
                            ->columns(2)
                            ->collapsible(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil'),
        ];
    }
}