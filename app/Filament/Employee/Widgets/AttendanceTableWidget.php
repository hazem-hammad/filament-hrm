<?php

namespace App\Filament\Employee\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AttendanceTableWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 2;

    protected static ?string $heading = 'Recent Attendance (Last 7 Days)';

    protected static string $view = 'filament.employee.widgets.attendance-table-widget';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->where('employee_id', auth()->id())
                    ->whereBetween('date', [now()->subDays(6), now()])
                    ->with('workPlan')
                    ->latest('date')
            )
            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('day_name')
                    ->label('Day')
                    ->getStateUsing(
                        fn(Attendance $record): string =>
                        Carbon::parse($record->date)->format('l')
                    )
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Saturday', 'Sunday' => 'warning',
                        default => 'primary',
                    }),

                TextColumn::make('check_in_time')
                    ->label('Check In')
                    ->time('H:i')
                    ->placeholder('--:--')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->iconColor('success'),

                TextColumn::make('check_out_time')
                    ->label('Check Out')
                    ->time('H:i')
                    ->placeholder('--:--')
                    ->icon('heroicon-o-arrow-left-circle')
                    ->iconColor('danger'),

                TextColumn::make('working_hours')
                    ->label('Hours')
                    ->getStateUsing(function (Attendance $record): string {
                        if (!$record->check_in_time || !$record->check_out_time) {
                            return '--:--';
                        }

                        $checkIn = Carbon::parse($record->check_in_time);
                        $checkOut = Carbon::parse($record->check_out_time);
                        $duration = $checkIn->diff($checkOut);

                        return sprintf(
                            '%02d:%02d',
                            $duration->h + ($duration->days * 24),
                            $duration->i
                        );
                    })
                    ->badge()
                    ->color('success'),

                TextColumn::make('late_minutes')
                    ->label('Late')
                    ->getStateUsing(
                        fn(Attendance $record): string =>
                        $record->late_minutes > 0 ? $record->late_minutes . ' min' : 'On time'
                    )
                    ->badge()
                    ->color(
                        fn(Attendance $record): string =>
                        $record->late_minutes > 0 ? 'warning' : 'success'
                    ),

                TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function (Attendance $record): string {
                        if (!$record->check_in_time) {
                            return 'Absent';
                        } elseif (!$record->check_out_time) {
                            return 'Checked In';
                        } else {
                            return 'Complete';
                        }
                    })
                    ->badge()
                    ->color(function (Attendance $record): string {
                        if (!$record->check_in_time) {
                            return 'danger';
                        } elseif (!$record->check_out_time) {
                            return 'warning';
                        } else {
                            return 'success';
                        }
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->paginated(false)
            ->striped()
            ->emptyStateHeading('No attendance records found')
            ->emptyStateDescription('Your recent attendance records will appear here once you start checking in.')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }
}
