<?php

namespace App\Notifications;

use App\Mail\JobApplicationReceivedMail;
use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class JobApplicationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private JobApplication $jobApplication
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new JobApplicationReceivedMail($this->jobApplication))
            ->to($notifiable->routeNotificationForMail());
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'job_application_id' => $this->jobApplication->id,
            'job_id' => $this->jobApplication->job_id,
            'applicant_name' => $this->jobApplication->full_name,
            'applicant_email' => $this->jobApplication->email,
        ];
    }
}