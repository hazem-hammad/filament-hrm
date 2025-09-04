<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use Filament\Actions;
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
            ->modifyQueryUsing(fn($query) => $query->with(['employee', 'workPlan']))
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('day_name')
                    ->label('Day')
                    ->badge()
                    ->color('info')
                    ->searchable(false)
                    ->sortable(false),
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
                //     ->color('success')
                //     ->sortable('working_hours'),
                // Tables\Columns\TextColumn::make('missing_hours_label')
                //     ->label('Missing Hours')
                //     ->badge()
                //     ->color(fn($record): string => $record->missing_hours > 0 ? 'danger' : 'success')
                //     ->sortable('missing_hours'),
                // Tables\Columns\TextColumn::make('late_minutes_label')
                //     ->label('Late Status')
                //     ->badge()
                //     ->color(fn($record): string => $record->late_minutes > 0 ? 'warning' : 'success')
                //     ->sortable('late_minutes'),
                Tables\Columns\IconColumn::make('is_manual')
                    ->label('Manual')
                    ->boolean()
                    ->trueIcon('heroicon-o-pencil')
                    ->falseIcon('heroicon-o-computer-desktop')
                    ->trueColor('warning')
                    ->falseColor('info')
                    ->tooltip(fn($record): string => $record->is_manual ? 'Manually added' : 'System recorded'),
                Tables\Columns\TextColumn::make('workPlan.name')
                    ->label('Work Plan')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('work_plan_id')
                    ->label('Work Plan')
                    ->relationship('workPlan', 'name')
                    ->searchable()
                    ->preload(),
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
                Tables\Filters\TernaryFilter::make('is_manual')
                    ->label('Entry Type')
                    ->trueLabel('Manual entries')
                    ->falseLabel('System entries')
                    ->native(false),
                Tables\Filters\Filter::make('late_attendance')
                    ->label('Late Attendance')
                    ->query(fn($query) => $query->where('late_minutes', '>', 0))
                    ->toggle(),
                Tables\Filters\Filter::make('missing_hours')
                    ->label('Missing Hours')
                    ->query(fn($query) => $query->where('missing_hours', '>', 0))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Records')
                ->badge(Attendance::count())
                ->badgeColor('gray'),
            'today' => Tab::make("Today's Attendance")
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('date', today()))
                ->badge(Attendance::whereDate('date', today())->count())
                ->badgeColor('primary'),
            'this_week' => Tab::make('This Week')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]))
                ->badge(Attendance::whereBetween('date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count())
                ->badgeColor('info'),
            'late_today' => Tab::make('Late Today')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('date', today())->where('late_minutes', '>', 0))
                ->badge(Attendance::whereDate('date', today())->where('late_minutes', '>', 0)->count())
                ->badgeColor('warning'),
            'missing_hours' => Tab::make('Missing Hours')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('missing_hours', '>', 0))
                ->badge(Attendance::where('missing_hours', '>', 0)->count())
                ->badgeColor('danger'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Attendance')
                ->icon('heroicon-o-plus'),
        ];
    }
}
