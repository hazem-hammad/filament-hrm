<?php

namespace App\Filament\Resources\WorkPlanResource\Pages;

use App\Enum\WorkingDay;
use App\Filament\Resources\WorkPlanResource;
use Filament\Actions;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewWorkPlan extends ViewRecord
{
    protected static string $resource = WorkPlanResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Work Plan Details')
                    ->schema([
                        Components\Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->schema([
                                Components\TextEntry::make('name')
                                    ->label('Work Plan Name')
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-briefcase'),
                                Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                                    ->icon(fn(bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                Components\TextEntry::make('start_time')
                                    ->label('Start Time')
                                    ->time('H:i')
                                    ->icon('heroicon-o-clock'),
                                Components\TextEntry::make('end_time')
                                    ->label('End Time')
                                    ->time('H:i')
                                    ->icon('heroicon-o-clock'),
                                Components\TextEntry::make('working_days_labels')
                                    ->label('Working Days')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-calendar-days')
                                    ->columnSpanFull(),
                                Components\TextEntry::make('permission_minutes_label')
                                    ->label('Grace Period')
                                    ->badge()
                                    ->color(fn($record): string => $record->permission_minutes > 0 ? 'warning' : 'gray')
                                    ->icon('heroicon-o-clock')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->columns(2),

                Components\Section::make('Assigned Employees')
                    ->schema([
                        Components\RepeatableEntry::make('employees')
                            ->label('')
                            ->schema([
                                Components\Grid::make([
                                    'default' => 1,
                                    'md' => 4,
                                ])
                                    ->schema([
                                        Components\TextEntry::make('employee_id')
                                            ->label('Employee ID')
                                            ->badge()
                                            ->color('gray'),
                                        Components\TextEntry::make('name')
                                            ->label('Employee Name')
                                            ->weight('bold')
                                            ->icon('heroicon-o-user'),
                                        Components\TextEntry::make('email')
                                            ->label('Email')
                                            ->icon('heroicon-o-envelope')
                                            ->copyable(),
                                        Components\TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                                            ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive'),
                                    ])
                            ])
                            ->columnSpanFull()
                            ->columns(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil'),
            Actions\DeleteAction::make()
                ->visible(fn(): bool => $this->record->employees()->count() === 0)
                ->modalDescription('This will permanently delete the work plan.')
                ->icon('heroicon-o-trash'),
        ];
    }
}
