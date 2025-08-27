<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class EmployeeWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $temporaryPassword,
        public string $passwordSetupToken
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $setupUrl = URL::signedRoute('employee.setup-password', [
            'token' => $this->passwordSetupToken,
            'employee' => $notifiable->id,
        ]);

        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name'))
            ->greeting('Welcome ' . $notifiable->name . '!')
            ->line('Your employee account has been created successfully.')
            ->line('Employee ID: ' . $notifiable->employee_id)
            ->line('Email: ' . $notifiable->email)
            ->line('Department: ' . $notifiable->department?->name)
            ->line('Position: ' . $notifiable->position?->name)
            ->line('Please click the button below to set up your password and complete your account setup.')
            ->action('Set Up Password', $setupUrl)
            ->line('This link will expire in 24 hours.')
            ->line('If you have any questions, please contact your HR department.')
            ->line('Welcome aboard!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'employee_id' => $notifiable->id,
            'employee_name' => $notifiable->name,
            'setup_token' => $this->passwordSetupToken,
        ];
    }
}
