<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

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
                    ])
                    ->columnSpanFull(),

                // Documents Section
                Components\Section::make('Documents')
                    ->schema([
                        Components\SpatieMediaLibraryImageEntry::make('documents')
                            ->label('Employee Documents')
                            ->collection('documents')
                            ->conversion('thumb')
                            ->limit(10)
                            ->columnSpan(2),
                        
                        Components\SpatieMediaLibraryImageEntry::make('profile')
                            ->label('Profile Image')
                            ->collection('profile')
                            ->conversion('thumb')
                            ->limit(1)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
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
