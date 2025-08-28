<?php

namespace App\Filament\Resources\AttendanceTypeResource\Pages;

use App\Filament\Resources\AttendanceTypeResource;
use Filament\Actions;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewAttendanceType extends ViewRecord
{
    protected static string $resource = AttendanceTypeResource::class;

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
                                    ->label('Attendance Type Name')
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

                        // Limits & Restrictions Section
                        Components\Section::make('Limits & Restrictions')
                            ->schema([
                                Components\TextEntry::make('has_limit')
                                    ->label('Has Limits')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'warning' : 'success')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Limited' : 'Unlimited')
                                    ->icon('heroicon-o-exclamation-triangle'),
                                
                                Components\Grid::make(3)
                                    ->schema([
                                        Components\TextEntry::make('max_hours_per_month')
                                            ->label('Max Hours/Month')
                                            ->suffix(' hours')
                                            ->placeholder('No limit')
                                            ->formatStateUsing(fn (?int $state): string => 
                                                $state ? $state . ' hours' : 'No limit'
                                            )
                                            ->visible(fn ($record) => $record->has_limit),
                                        Components\TextEntry::make('max_requests_per_month')
                                            ->label('Max Requests/Month')
                                            ->suffix(' requests')
                                            ->placeholder('No limit')
                                            ->formatStateUsing(fn (?int $state): string => 
                                                $state ? $state . ' requests' : 'No limit'
                                            )
                                            ->visible(fn ($record) => $record->has_limit),
                                        Components\TextEntry::make('max_hours_per_request')
                                            ->label('Max Hours/Request')
                                            ->suffix(' hours')
                                            ->placeholder('No limit')
                                            ->formatStateUsing(fn (?float $state): string => 
                                                $state ? $state . ' hours' : 'No limit'
                                            )
                                            ->visible(fn ($record) => $record->has_limit),
                                    ])
                                    ->visible(fn ($record) => $record->has_limit),

                                Components\TextEntry::make('limit_summary')
                                    ->label('Limits Summary')
                                    ->badge()
                                    ->color(fn ($record): string => $record->has_limit ? 'warning' : 'success')
                                    ->icon('heroicon-o-information-circle')
                                    ->columnSpanFull(),

                                Components\TextEntry::make('requires_approval')
                                    ->label('Approval Required')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'warning' : 'success')
                                    ->formatStateUsing(fn (bool $state): string => 
                                        $state ? 'Multi-level approval required' : 'No approval required'
                                    )
                                    ->icon('heroicon-o-check-circle'),
                            ])
                            ->columns(1),

                        // System Information Section
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