<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
class EmployeeWelcomeNotification extends Notification
{

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
        return (new MailMessage)
            ->subject('🎉 Welcome to ' . config('app.name') . ' - Your Journey Begins!')
            ->from(config('mail.from.address'), get_setting('company_name', 'HRM System'))
            ->view('emails.employee-welcome', [
                'employee' => $notifiable,
                'temporaryPassword' => $this->temporaryPassword,
                'loginUrl' => url('/employee/login'),
                'companyName' => config('app.name'),
                'companyLogo' => get_setting('logo_light', '/images/logos/logo-light.svg'),
            ]);
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
