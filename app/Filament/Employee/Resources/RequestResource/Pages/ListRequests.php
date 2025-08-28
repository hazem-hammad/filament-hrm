<?php

namespace App\Filament\Employee\Resources\RequestResource\Pages;

use App\Filament\Employee\Resources\RequestResource;
use App\Models\Request;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class ListRequests extends ListRecords
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Request'),
        ];
    }

    public function getTabs(): array
    {
        $employee = auth('employee')->user();
        $hasTeam = $employee->directReports()->exists();

        $tabs = [
            'my_requests' => Tab::make('My Requests')
                ->modifyQueryUsing(fn(Builder $query) => 
                    $query->where('employee_id', $employee->id)
                        ->with(['requestable', 'approver'])
                )
                ->badge(Request::where('employee_id', $employee->id)->count()),
        ];

        // Only show Team Requests tab if the employee is a manager (has direct reports)
        if ($hasTeam) {
            $teamEmployeeIds = $employee->directReports()->pluck('id');
            $teamRequestsCount = Request::whereIn('employee_id', $teamEmployeeIds)->count();

            $tabs['team_requests'] = Tab::make('Team Requests')
                ->modifyQueryUsing(function (Builder $query) use ($teamEmployeeIds) {
                    return $query->whereIn('employee_id', $teamEmployeeIds)
                        ->with(['employee', 'requestable', 'approver']);
                })
                ->badge($teamRequestsCount);
        }

        return $tabs;
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'my_requests';
    }


    protected function getTableColumns(): array
    {
        $employee = auth('employee')->user();
        $activeTab = $this->activeTab ?? 'my_requests';

        // Get the base columns from the resource
        $columns = RequestResource::table(\Filament\Tables\Table::make())->getColumns();

        // If viewing team requests, add employee name column at the beginning
        if ($activeTab === 'team_requests' && $employee->directReports()->exists()) {
            $employeeColumn = Tables\Columns\TextColumn::make('employee.name')
                ->label('Employee')
                ->searchable()
                ->sortable();

            // Insert at the beginning
            array_unshift($columns, $employeeColumn);
        }

        return $columns;
    }

    protected function getTableActions(): array
    {
        $employee = auth('employee')->user();
        $activeTab = $this->activeTab ?? 'my_requests';

        if ($activeTab === 'team_requests' && $employee->directReports()->exists()) {
            // Actions for team requests (approval/rejection)
            return [
                \Filament\Tables\Actions\ViewAction::make(),
                \Filament\Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) use ($employee) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => $employee->id,
                            'approved_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Approve Request')
                    ->modalDescription('Are you sure you want to approve this request?')
                    ->visible(fn($record) => $record->canBeApproved()),

                \Filament\Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('admin_notes')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) use ($employee) {
                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => $employee->id,
                            'approved_at' => now(),
                            'admin_notes' => $data['admin_notes'],
                        ]);
                    })
                    ->modalHeading('Reject Request')
                    ->modalSubmitActionLabel('Reject')
                    ->visible(fn($record) => $record->canBeRejected()),
            ];
        }

        // Default actions for my requests
        return [
            \Filament\Tables\Actions\ViewAction::make(),
            \Filament\Tables\Actions\EditAction::make()
                ->visible(fn($record) => $record->status === 'pending'),
            \Filament\Tables\Actions\Action::make('cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->action(function ($record) {
                    $record->update(['status' => 'cancelled']);
                })
                ->requiresConfirmation()
                ->modalHeading('Cancel Request')
                ->modalDescription('Are you sure you want to cancel this request?')
                ->visible(fn($record) => $record->canBeCancelled()),
        ];
    }
}
