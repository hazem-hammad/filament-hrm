<?php

namespace App\Filament\Employee\Resources\RequestResource\Pages;

use App\Filament\Employee\Resources\RequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewRequest extends ViewRecord
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        $employee = auth('employee')->user();
        $isMyRequest = $this->record->employee_id === $employee->id;
        $isTeamRequest = $employee->directReports()->pluck('id')->contains($this->record->employee_id);

        $actions = [];

        if ($isMyRequest) {
            // Actions for my requests
            $actions[] = Actions\EditAction::make()
                ->visible($this->record->status === 'pending');

            $actions[] = Actions\Action::make('cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->action(function () {
                    $this->record->update(['status' => 'cancelled']);

                    \Filament\Notifications\Notification::make()
                        ->title('Request Cancelled')
                        ->body('Your request has been cancelled successfully.')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Cancel Request')
                ->modalDescription('Are you sure you want to cancel this request?')
                ->visible($this->record->status === 'pending');
        } elseif ($isTeamRequest) {
            // Actions for team requests (manager actions)
            $actions[] = Actions\Action::make('approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () use ($employee) {
                    $this->record->update([
                        'status' => 'approved',
                        'approved_by' => $employee->id,
                        'approved_at' => now(),
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Request Approved')
                        ->body('The request has been approved successfully.')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Approve Request')
                ->modalDescription('Are you sure you want to approve this request?')
                ->visible($this->record->canBeApproved());

            $actions[] = Actions\Action::make('reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Textarea::make('admin_notes')
                        ->label('Rejection Reason')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) use ($employee) {
                    $this->record->update([
                        'status' => 'rejected',
                        'approved_by' => $employee->id,
                        'approved_at' => now(),
                        'admin_notes' => $data['admin_notes'],
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Request Rejected')
                        ->body('The request has been rejected.')
                        ->success()
                        ->send();
                })
                ->modalHeading('Reject Request')
                ->modalSubmitActionLabel('Reject')
                ->visible($this->record->canBeRejected());
        }

        return $actions;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $employee = auth('employee')->user();
        $isTeamRequest = $employee->directReports()->pluck('id')->contains($this->record->employee_id);

        $sections = [];

        // Basic Request Information
        $requestInfoEntries = [
            Infolists\Components\TextEntry::make('request_type')
                ->label('Request Type')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'vacation' => 'info',
                    'attendance' => 'primary',
                    default => 'gray',
                })
                ->formatStateUsing(fn(string $state): string => ucfirst($state)),

            Infolists\Components\TextEntry::make('requestable.name')
                ->label(fn($record) => $record->request_type === 'vacation' ? 'Vacation Type' : 'Attendance Type'),

            Infolists\Components\TextEntry::make('status')
                ->label('Status')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    'cancelled' => 'gray',
                    default => 'gray',
                })
                ->formatStateUsing(fn(string $state): string => ucfirst($state)),
        ];

        // Add employee name if viewing team request
        if ($isTeamRequest) {
            array_unshift(
                $requestInfoEntries,
                Infolists\Components\TextEntry::make('employee.name')
                    ->label('Employee')
            );
        }

        $sections[] = Infolists\Components\Section::make('Request Information')
            ->schema($requestInfoEntries)
            ->columns(2);

        // Date/Time Information (conditional based on request type)
        if ($this->record->request_type === 'vacation') {
            $sections[] = Infolists\Components\Section::make('Vacation Details')
                ->schema([
                    Infolists\Components\TextEntry::make('start_date')
                        ->label('Start Date')
                        ->date(),
                    Infolists\Components\TextEntry::make('end_date')
                        ->label('End Date')
                        ->date(),
                    Infolists\Components\TextEntry::make('total_days')
                        ->label('Total Days')
                        ->numeric(),
                ])
                ->columns(3);
        } else {
            $sections[] = Infolists\Components\Section::make('Attendance Details')
                ->schema([
                    Infolists\Components\TextEntry::make('request_date')
                        ->label('Request Date')
                        ->date(),
                    Infolists\Components\TextEntry::make('start_time')
                        ->label('Start Time')
                        ->time('H:i'),
                    Infolists\Components\TextEntry::make('end_time')
                        ->label('End Time')
                        ->time('H:i'),
                    Infolists\Components\TextEntry::make('hours')
                        ->label('Total Hours')
                        ->numeric(2),
                ])
                ->columns(4);
        }

        // Reason
        $sections[] = Infolists\Components\Section::make('Request Details')
            ->schema([
                Infolists\Components\TextEntry::make('reason')
                    ->label('Reason')
                    ->columnSpanFull(),
            ]);

        // Approval Information (if applicable)
        if (in_array($this->record->status, ['approved', 'rejected'])) {
            $sections[] = Infolists\Components\Section::make('Approval Information')
                ->schema([
                    Infolists\Components\TextEntry::make('approver.name')
                        ->label(fn($record) => $record->status === 'rejected' ? 'Rejected By' : 'Approved By'),
                    Infolists\Components\TextEntry::make('approved_at')
                        ->label('Decision Date')
                        ->dateTime(),
                    Infolists\Components\TextEntry::make('admin_notes')
                        ->label(fn($record) => $record->status === 'rejected' ? 'Rejection Reason' : 'Admin Notes')
                        ->columnSpanFull()
                        ->visible(fn($record) => !empty($record->admin_notes))
                        ->color(fn($record) => $record->status === 'rejected' ? 'danger' : null),
                ])
                ->columns(2);
        }

        // Timestamps
        $sections[] = Infolists\Components\Section::make('Timeline')
            ->schema([
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Submitted At')
                    ->dateTime(),
                Infolists\Components\TextEntry::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime(),
            ])
            ->columns(2)
            ->collapsible();

        return $infolist->schema($sections);
    }
}
