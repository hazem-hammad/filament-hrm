<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use Filament\Actions;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewRequest extends ViewRecord
{
    protected static string $resource = RequestResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                ])
                    ->schema([
                        // Request Information Section
                        Components\Section::make('Request Information')
                            ->schema([
                                Components\TextEntry::make('employee.name')
                                    ->label('Employee')
                                    ->icon('heroicon-o-user'),
                                Components\TextEntry::make('request_type')
                                    ->label('Request Type')
                                    ->badge()
                                    ->color('request_type_color')
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                                Components\TextEntry::make('requestable.name')
                                    ->label(fn ($record) => $record->isVacation() ? 'Vacation Type' : 'Attendance Type')
                                    ->badge()
                                    ->color('primary'),
                                Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color('status_color')
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                            ])
                            ->columns(2),

                        // Vacation Details Section
                        Components\Section::make('Vacation Details')
                            ->schema([
                                Components\TextEntry::make('start_date')
                                    ->label('Start Date')
                                    ->date()
                                    ->icon('heroicon-o-calendar'),
                                Components\TextEntry::make('end_date')
                                    ->label('End Date')
                                    ->date()
                                    ->icon('heroicon-o-calendar'),
                                Components\TextEntry::make('total_days')
                                    ->label('Total Days')
                                    ->suffix(' days')
                                    ->icon('heroicon-o-clock'),
                            ])
                            ->visible(fn ($record) => $record->isVacation())
                            ->columns(2),

                        // Attendance Details Section
                        Components\Section::make('Attendance Details')
                            ->schema([
                                Components\TextEntry::make('request_date')
                                    ->label('Request Date')
                                    ->date()
                                    ->icon('heroicon-o-calendar'),
                                Components\TextEntry::make('hours')
                                    ->label('Hours')
                                    ->suffix(' hours')
                                    ->icon('heroicon-o-clock'),
                                Components\TextEntry::make('start_time')
                                    ->label('Start Time')
                                    ->time()
                                    ->icon('heroicon-o-play'),
                                Components\TextEntry::make('end_time')
                                    ->label('End Time')
                                    ->time()
                                    ->icon('heroicon-o-stop'),
                            ])
                            ->visible(fn ($record) => $record->isAttendance())
                            ->columns(2),

                        // Additional Information Section
                        Components\Section::make('Additional Information')
                            ->schema([
                                Components\TextEntry::make('reason')
                                    ->label('Reason')
                                    ->placeholder('No reason provided')
                                    ->columnSpanFull(),
                                Components\TextEntry::make('admin_notes')
                                    ->label('Admin Notes')
                                    ->placeholder('No admin notes')
                                    ->visible(fn ($record) => filled($record->admin_notes))
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        // Approval Information Section
                        Components\Section::make('Approval Information')
                            ->schema([
                                Components\TextEntry::make('approver.name')
                                    ->label('Approved By')
                                    ->icon('heroicon-o-user')
                                    ->placeholder('Not yet approved'),
                                Components\TextEntry::make('approved_at')
                                    ->label('Approved At')
                                    ->dateTime()
                                    ->icon('heroicon-o-check-circle')
                                    ->placeholder('Not yet approved'),
                                Components\TextEntry::make('created_at')
                                    ->label('Requested At')
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