<?php

namespace App\Filament\Employee\Widgets;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class CheckInOutWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string $view = 'filament.employee.widgets.check-in-out-widget';

    protected int | string | array $columnSpan = 1;

    public function getTodayAttendance()
    {
        return Attendance::where('employee_id', auth()->id())
            ->whereDate('date', today())
            ->first();
    }

    public function getGreeting(): string
    {
        $hour = now()->hour;

        if ($hour < 12) {
            return 'Good morning';
        } elseif ($hour < 17) {
            return 'Good afternoon';
        } else {
            return 'Good evening';
        }
    }

    public function getEmployeeName(): string
    {
        return auth()->user()->name ?? 'Employee';
    }

    public function isCheckedIn(): bool
    {
        $attendance = $this->getTodayAttendance();
        return $attendance && $attendance->check_in_time && !$attendance->check_out_time;
    }

    public function hasCheckedOut(): bool
    {
        $attendance = $this->getTodayAttendance();
        return $attendance && $attendance->check_in_time && $attendance->check_out_time;
    }

    public function canCheckIn(): bool
    {
        $attendance = $this->getTodayAttendance();
        return !$attendance; // No attendance record for today
    }

    public function getCheckInTime(): ?string
    {
        $attendance = $this->getTodayAttendance();
        return $attendance?->check_in_time?->format('H:i');
    }

    public function getCheckOutTime(): ?string
    {
        $attendance = $this->getTodayAttendance();
        return $attendance?->check_out_time?->format('H:i');
    }

    public function getWorkDuration(): string
    {
        $attendance = $this->getTodayAttendance();
        if (!$attendance || !$attendance->check_in_time) {
            return '00:00:00';
        }

        $checkIn = Carbon::parse($attendance->check_in_time);
        $endTime = $attendance->check_out_time ?
            Carbon::parse($attendance->check_out_time) :
            now();

        $duration = $checkIn->diff($endTime);
        return sprintf(
            '%02d:%02d:%02d',
            $duration->h + ($duration->days * 24),
            $duration->i,
            $duration->s
        );
    }

    public function checkInAction(): Action
    {
        return Action::make('checkIn')
            ->label('Check In')
            ->color('success')
            ->size('xl')
            ->requiresConfirmation()
            ->modalHeading('Check In')
            ->modalDescription('Are you sure you want to check in now?')
            ->modalSubmitActionLabel('Check In')
            ->action(function () {
                $employee = Employee::find(auth()->id());
                $workPlan = $employee->workPlans()->active()->first();

                if (!$workPlan) {
                    Notification::make()
                        ->title('No Work Plan Assigned')
                        ->body('Please contact HR to assign a work plan before checking in.')
                        ->danger()
                        ->send();
                    return;
                }

                // Check if already checked in today
                $existingAttendance = $this->getTodayAttendance();
                if ($existingAttendance) {
                    Notification::make()
                        ->title('Already Checked In')
                        ->body('You have already checked in today.')
                        ->warning()
                        ->send();
                    return;
                }

                Attendance::create([
                    'employee_id' => auth()->id(),
                    'work_plan_id' => $workPlan->id,
                    'date' => today(),
                    'check_in_time' => now()->format('H:i:s'),
                    'is_manual' => false,
                ]);

                Notification::make()
                    ->title('Checked In Successfully')
                    ->body('Welcome to work! Have a productive day.')
                    ->success()
                    ->send();

                $this->redirect(request()->header('Referer'));
            });
    }

    public function checkOutAction(): Action
    {
        return Action::make('checkOut')
            ->label('Check Out')
            ->color('danger')
            ->size('xl')
            ->requiresConfirmation()
            ->modalHeading('Check Out')
            ->modalDescription('Are you sure you want to check out now?')
            ->modalSubmitActionLabel('Check Out')
            ->action(function () {
                $attendance = $this->getTodayAttendance();

                if (!$attendance) {
                    Notification::make()
                        ->title('No Check-In Found')
                        ->body('You need to check in first before checking out.')
                        ->warning()
                        ->send();
                    return;
                }

                if ($attendance->check_out_time) {
                    Notification::make()
                        ->title('Already Checked Out')
                        ->body('You have already checked out today.')
                        ->warning()
                        ->send();
                    return;
                }

                $attendance->update([
                    'check_out_time' => now()->format('H:i:s'),
                ]);

                Notification::make()
                    ->title('Checked Out Successfully')
                    ->body('Thank you for your work today! See you tomorrow.')
                    ->success()
                    ->send();

                $this->redirect(request()->header('Referer'));
            });
    }

    protected function getViewData(): array
    {
        return [
            'greeting' => $this->getGreeting(),
            'employeeName' => $this->getEmployeeName(),
            'canCheckIn' => $this->canCheckIn(),
            'isCheckedIn' => $this->isCheckedIn(),
            'hasCheckedOut' => $this->hasCheckedOut(),
            'checkInTime' => $this->getCheckInTime(),
            'checkOutTime' => $this->getCheckOutTime(),
            'workDuration' => $this->getWorkDuration(),
            'todayDate' => now()->format('d M Y'),
            'currentTime' => now()->format('h:i A'),
        ];
    }
}
