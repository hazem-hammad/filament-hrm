<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Enum\EmployeeLevel;
use App\Filament\Resources\EmployeeResource;
use App\Filament\Widgets\EmployeeStatsWidget;
use App\Models\Employee;
use App\Models\Department;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Database\Eloquent\Builder;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['department', 'position', 'manager', 'workPlans']))
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('profile')
                    ->label('Photo')
                    ->collection('profile')
                    ->conversion('thumb')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(
                        fn(Employee $record): string =>
                        'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF'
                    ),
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Employee ID copied')
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('position.name')
                    ->label('Position')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Employee $record): string => $record->level->label()),
                Tables\Columns\TextColumn::make('manager.name')
                    ->label('Reports To')
                    ->placeholder('No Manager')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_date_of_joining')
                    ->label('Joining Date')
                    ->date()
                    ->sortable()
                    ->description(fn(Employee $record): string => $record->company_date_of_joining->diffForHumans()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('position_id')
                    ->label('Position')
                    ->relationship('position', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('level')
                    ->label('Level')
                    ->options(EmployeeLevel::options())
                    ->searchable()
                    ->native(false)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('reporting_to')
                    ->label('Reports To')
                    ->relationship('manager', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('work_plans')
                    ->label('Work Plan')
                    ->relationship('workPlans', 'name', fn($query) => $query->active())
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\Filter::make('joining_date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('joined_from')
                            ->label('Joined From'),
                        \Filament\Forms\Components\DatePicker::make('joined_until')
                            ->label('Joined Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['joined_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('company_date_of_joining', '>=', $date)
                            )
                            ->when(
                                $data['joined_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('company_date_of_joining', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['joined_from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Joined from ' . \Carbon\Carbon::parse($data['joined_from'])->toFormattedDateString())
                                ->removeField('joined_from');
                        }
                        if ($data['joined_until'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Joined until ' . \Carbon\Carbon::parse($data['joined_until'])->toFormattedDateString())
                                ->removeField('joined_until');
                        }
                        return $indicators;
                    }),
                Tables\Filters\Filter::make('age_range')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('age_from')
                            ->label('Age From')
                            ->numeric()
                            ->minValue(18)
                            ->maxValue(100),
                        \Filament\Forms\Components\TextInput::make('age_to')
                            ->label('Age To')
                            ->numeric()
                            ->minValue(18)
                            ->maxValue(100),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['age_from'] ?? null, function (Builder $query, $age): Builder {
                                return $query->whereDate('date_of_birth', '<=', now()->subYears($age));
                            })
                            ->when($data['age_to'] ?? null, function (Builder $query, $age): Builder {
                                return $query->whereDate('date_of_birth', '>=', now()->subYears($age + 1));
                            });
                    }),
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->native(false),
                Tables\Filters\TernaryFilter::make('status')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn(Employee $record): string => $record->status ? 'Deactivate' : 'Activate')
                        ->icon(fn(Employee $record): string => $record->status ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn(Employee $record): string => ($record->status ?? false) ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalDescription(
                            fn(Employee $record): string =>
                            $record->status
                                ? 'Are you sure you want to deactivate this employee?'
                                : 'Are you sure you want to activate this employee?'
                        )
                        ->action(fn(Employee $record) => $record->update(['status' => !$record->status])),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate_selected')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Are you sure you want to activate the selected employees?')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $records->each(fn($record) => $record->update(['status' => true]));
                        }),
                    Tables\Actions\BulkAction::make('deactivate_selected')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalDescription('Are you sure you want to deactivate the selected employees?')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $records->each(fn($record) => $record->update(['status' => false]));
                        }),
                    Tables\Actions\BulkAction::make('export_selected')
                        ->label('Export Selected')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->action(function ($records) {
                            return response()->streamDownload(function () use ($records) {
                                $csvData = "Name,Employee ID,Email,Phone,Department,Position,Level,Status,Joining Date\n";
                                foreach ($records as $record) {
                                    $csvData .= sprintf(
                                        "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                                        $record->name,
                                        $record->employee_id,
                                        $record->email,
                                        $record->phone,
                                        $record->department?->name ?? '',
                                        $record->position?->name ?? '',
                                        $record->level?->label() ?? '',
                                        $record->status ? 'Active' : 'Inactive',
                                        $record->company_date_of_joining->format('Y-m-d')
                                    );
                                }
                                echo $csvData;
                            }, 'employees-export-' . now()->format('Y-m-d-H-i-s') . '.csv');
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No employees found')
            ->emptyStateDescription('Get started by creating your first employee.')
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Employee')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
