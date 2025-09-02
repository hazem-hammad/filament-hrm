<?php

namespace App\Notifications;

use Illuminate\Notifications\Notifiable;

class ApplicantNotifiable
{
    use Notifiable;

    public function __construct(
        private string $email,
        private string $name = ''
    ) {
        //
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail(): string
    {
        return $this->email;
    }

    /**
     * Get the name for notifications.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
