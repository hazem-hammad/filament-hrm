<?php

namespace App\Filament\Employee\Resources\AttendanceResource\Pages;

use App\Filament\Employee\Resources\AttendanceResource;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['workPlan']))
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('day_name')
                    ->label('Day')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('workPlan.name')
                    ->label('Work Plan')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('check_in_time')
                    ->label('Check In')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_out_time')
                    ->label('Check Out')
                    ->time('H:i')
                    ->placeholder('Not checked out')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('working_hours_label')
                //     ->label('Working Hours')
                //     ->badge()
                //     ->color('success'),
                // Tables\Columns\TextColumn::make('missing_hours_label')
                //     ->label('Missing Hours')
                //     ->badge()
                //     ->color(fn($record): string => $record->missing_hours > 0 ? 'danger' : 'success'),
                // Tables\Columns\TextColumn::make('late_minutes_label')
                //     ->label('Status')
                //     ->badge()
                //     ->color(fn($record): string => $record->late_minutes > 0 ? 'warning' : 'success'),
                Tables\Columns\IconColumn::make('is_manual')
                    ->label('Entry Type')
                    ->boolean()
                    ->trueIcon('heroicon-o-pencil')
                    ->falseIcon('heroicon-o-computer-desktop')
                    ->trueColor('warning')
                    ->falseColor('info')
                    ->tooltip(fn($record): string => $record->is_manual ? 'Manual entry' : 'System recorded')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from_date'], fn($q) => $q->where('date', '>=', $data['from_date']))
                            ->when($data['to_date'], fn($q) => $q->where('date', '<=', $data['to_date']));
                    }),
                Tables\Filters\Filter::make('late_attendance')
                    ->label('Late Attendance Only')
                    ->query(fn($query) => $query->where('late_minutes', '>', 0))
                    ->toggle(),
                Tables\Filters\Filter::make('missing_hours')
                    ->label('Missing Hours Only')
                    ->query(fn($query) => $query->where('missing_hours', '>', 0))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Attendance Details')
                    ->modalContent(fn($record) => view('filament.employee.attendance.view', compact('record')))
                    ->modalWidth('lg'),
            ])
            ->emptyStateHeading('No Attendance Records')
            ->emptyStateDescription('Your attendance records will appear here once they are recorded.')
            ->emptyStateIcon('heroicon-o-clock');
    }

    public function getTabs(): array
    {
        $employeeId = auth()->id();

        return [
            'all' => Tab::make('All Records')
                ->badge(Attendance::where('employee_id', $employeeId)->count())
                ->badgeColor('gray'),
            'this_month' => Tab::make('This Month')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereMonth('date', now()->month)->whereYear('date', now()->year))
                ->badge(Attendance::where('employee_id', $employeeId)->whereMonth('date', now()->month)->whereYear('date', now()->year)->count())
                ->badgeColor('primary'),
            'this_week' => Tab::make('This Week')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]))
                ->badge(Attendance::where('employee_id', $employeeId)->whereBetween('date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count())
                ->badgeColor('info'),
            'late_records' => Tab::make('Late Records')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('late_minutes', '>', 0))
                ->badge(Attendance::where('employee_id', $employeeId)->where('late_minutes', '>', 0)->count())
                ->badgeColor('warning'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // No create action for employees
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Employee\Widgets\AttendanceStatsWidget::class,
        ];
    }
}
