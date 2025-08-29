<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Enum\EmployeeLevel;
use App\Filament\Resources\EmployeeResource;
use App\Models\Employee;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['department', 'position', 'manager', 'workPlans']))
            ->columns([
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('Employee ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position.name')
                    ->label('Position')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->label('Level')
                    ->formatStateUsing(fn(EmployeeLevel $state): string => $state->label())
                    ->badge()
                    ->color(fn(EmployeeLevel $state): string => match($state) {
                        EmployeeLevel::INTERNSHIP => 'gray',
                        EmployeeLevel::ENTRY => 'info',
                        EmployeeLevel::JUNIOR => 'success',
                        EmployeeLevel::MID => 'warning',
                        EmployeeLevel::SENIOR => 'danger',
                        EmployeeLevel::LEAD => 'indigo',
                        EmployeeLevel::MANAGER => 'purple',
                    })
                    ->searchable()
                    ->sortable(),
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
                    ->sortable(),
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
                    ->preload(),
                Tables\Filters\SelectFilter::make('position_id')
                    ->label('Position')
                    ->relationship('position', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('level')
                    ->label('Level')
                    ->options(EmployeeLevel::options())
                    ->searchable()
                    ->native(false),
                Tables\Filters\SelectFilter::make('reporting_to')
                    ->label('Reports To')
                    ->relationship('manager', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('work_plans')
                    ->label('Work Plan')
                    ->relationship('workPlans', 'name', fn($query) => $query->active())
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('status')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),
            ])
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
