<?php

namespace App\Notifications;

use App\Mail\JobStageUpdateMail;
use App\Models\JobApplication;
use App\Models\JobStage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class JobApplicationStageChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private JobApplication $jobApplication,
        private JobStage $newStage
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
        return (new JobStageUpdateMail($this->jobApplication, $this->newStage))
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
            'new_stage_id' => $this->newStage->id,
            'new_stage_name' => $this->newStage->name,
        ];
    }
}
