<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Http\Resources\MediaResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function resolveRecord(int|string $key): \Illuminate\Database\Eloquent\Model
    {
        return static::getResource()::resolveRecordRouteBinding($key)
            ->load([
                'documents.folder',
                'directReports.position',
                'directReports.department',
                'workPlans',
                'assets'
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                ])
                    ->schema([
                        // Personal Details Section
                        Components\Section::make('Personal Details')
                            ->schema([
                                Components\TextEntry::make('name')
                                    ->label('Full Name')
                                    ->icon('heroicon-o-user'),
                                Components\TextEntry::make('employee_id')
                                    ->label('Employee ID')
                                    ->badge()
                                    ->color('primary'),
                                Components\TextEntry::make('email')
                                    ->label('Email Address')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable(),
                                Components\TextEntry::make('phone')
                                    ->label('Phone Number')
                                    ->icon('heroicon-o-phone')
                                    ->copyable(),
                                Components\TextEntry::make('date_of_birth')
                                    ->label('Date of Birth')
                                    ->date()
                                    ->icon('heroicon-o-cake'),
                                Components\TextEntry::make('gender')
                                    ->label('Gender')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'male' => 'blue',
                                        'female' => 'pink',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                                Components\TextEntry::make('address')
                                    ->label('Address')
                                    ->icon('heroicon-o-map-pin')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        // Company Details Section
                        Components\Section::make('Company Details')
                            ->schema([
                                Components\TextEntry::make('department.name')
                                    ->label('Department')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-building-office'),
                                Components\TextEntry::make('position.name')
                                    ->label('Position/Designation')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-briefcase'),
                                Components\TextEntry::make('manager.name')
                                    ->label('Reports To (Manager)')
                                    ->placeholder('No Manager Assigned')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-user-group')
                                    ->columnSpanFull(),
                                Components\TextEntry::make('company_date_of_joining')
                                    ->label('Date of Joining')
                                    ->date()
                                    ->icon('heroicon-o-calendar-days'),
                                Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive'),
                            ])
                            ->columns(2),

                        // Work Plans Section
                        Components\Section::make('Work Plans')
                            ->schema([
                                Components\RepeatableEntry::make('workPlans')
                                    ->label('Assigned Work Plans')
                                    ->schema([
                                        Components\TextEntry::make('name')
                                            ->label('Work Plan Name')
                                            ->badge()
                                            ->color('info'),
                                        Components\TextEntry::make('schedule')
                                            ->label('Schedule')
                                            ->icon('heroicon-o-clock'),
                                        Components\TextEntry::make('working_days_labels')
                                            ->label('Working Days')
                                            ->badge()
                                            ->color('success'),
                                        Components\TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                                            ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive'),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),

                        // Employee Documents Section
                        Components\Section::make('Employee Documents')
                            ->schema([
                                Components\RepeatableEntry::make('documents')
                                    ->label('Assigned Documents')
                                    ->schema([
                                        Components\TextEntry::make('name')
                                            ->label('Document Name')
                                            ->icon(fn($record) => $record->file_icon ?? 'heroicon-o-document')
                                            ->iconColor(fn($record) => $record->file_color ?? 'gray')
                                            ->weight('medium'),
                                        Components\TextEntry::make('description')
                                            ->label('Description')
                                            ->placeholder('No description')
                                            ->limit(50),
                                        Components\TextEntry::make('folder.name')
                                            ->label('Folder')
                                            ->badge()
                                            ->color('primary')
                                            ->placeholder('Root'),
                                        Components\TextEntry::make('formatted_size')
                                            ->label('Size')
                                            ->badge()
                                            ->color('success'),
                                        Components\TextEntry::make('file_type')
                                            ->label('Type')
                                            ->badge()
                                            ->color('info')
                                            ->formatStateUsing(fn(string $state): string => strtoupper($state)),
                                        Components\TextEntry::make('created_at')
                                            ->label('Uploaded')
                                            ->since()
                                            ->icon('heroicon-o-calendar'),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->placeholder('No documents assigned to this employee'),
                            ])
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->documents->count() > 0),

                        // Assigned Assets Section
                        Components\Section::make('Assigned Assets')
                            ->schema([
                                Components\RepeatableEntry::make('assets')
                                    ->label('Currently Assigned Assets')
                                    ->schema([
                                        Components\TextEntry::make('asset_id')
                                            ->label('Asset ID')
                                            ->badge()
                                            ->color('primary')
                                            ->copyable()
                                            ->weight('medium'),
                                        Components\TextEntry::make('name')
                                            ->label('Asset Name')
                                            ->icon('heroicon-o-computer-desktop')
                                            ->weight('medium')
                                            ->description(fn($record) => $record->brand && $record->model ? "{$record->brand} {$record->model}" : null),
                                        Components\TextEntry::make('category')
                                            ->label('Category')
                                            ->badge()
                                            ->color('info'),
                                        Components\TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn($state) => match($state) {
                                                \App\Enum\AssetStatus::ASSIGNED => 'success',
                                                \App\Enum\AssetStatus::AVAILABLE => 'warning',
                                                \App\Enum\AssetStatus::MAINTENANCE => 'danger',
                                                \App\Enum\AssetStatus::RETIRED => 'gray',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn($state) => $state->label()),
                                        Components\TextEntry::make('condition')
                                            ->label('Condition')
                                            ->badge()
                                            ->color(fn($state) => $state->color())
                                            ->formatStateUsing(fn($state) => $state->label()),
                                        Components\TextEntry::make('assigned_at')
                                            ->label('Assigned Date')
                                            ->date()
                                            ->icon('heroicon-o-calendar-days'),
                                        Components\TextEntry::make('location')
                                            ->label('Location')
                                            ->icon('heroicon-o-map-pin')
                                            ->placeholder('No location specified'),
                                        Components\TextEntry::make('serial_number')
                                            ->label('Serial Number')
                                            ->placeholder('Not specified')
                                            ->copyable(),
                                    ])
                                    ->columns(4)
                                    ->columnSpanFull()
                                    ->placeholder('No assets are currently assigned to this employee'),
                            ])
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->assets->count() > 0),

                        // Team Members Section (for managers)
                        Components\Section::make('Team Members')
                            ->schema([
                                Components\RepeatableEntry::make('directReports')
                                    ->label('Direct Reports')
                                    ->schema([
                                        Components\TextEntry::make('name')
                                            ->label('Employee Name')
                                            ->icon('heroicon-o-user')
                                            ->weight('medium'),
                                        Components\TextEntry::make('employee_id')
                                            ->label('Employee ID')
                                            ->badge()
                                            ->color('primary'),
                                        Components\TextEntry::make('position.name')
                                            ->label('Position')
                                            ->badge()
                                            ->color('warning')
                                            ->placeholder('No position assigned'),
                                        Components\TextEntry::make('department.name')
                                            ->label('Department')
                                            ->badge()
                                            ->color('success'),
                                        Components\TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                                            ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive'),
                                        Components\TextEntry::make('company_date_of_joining')
                                            ->label('Joining Date')
                                            ->date()
                                            ->icon('heroicon-o-calendar-days'),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->placeholder('This employee has no direct reports'),
                            ])
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->directReports->count() > 0),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon('heroicon-o-pencil'),
        ];
    }
}
