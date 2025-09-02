<?php

namespace App\Observers;

use App\Models\JobApplication;
use App\Models\JobStage;
use App\Notifications\ApplicantNotifiable;
use App\Notifications\JobApplicationStageChanged;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class JobApplicationObserver
{
    /**
     * Handle the JobApplication "created" event.
     */
    public function created(JobApplication $jobApplication): void
    {
        // Send notification for initial stage assignment
        if ($jobApplication->job_stage_id) {
            $this->sendStageNotification($jobApplication);
        }
    }

    /**
     * Handle the JobApplication "updated" event.
     */
    public function updated(JobApplication $jobApplication): void
    {
        // Check if job_stage_id has changed
        if ($jobApplication->isDirty('job_stage_id') && $jobApplication->job_stage_id) {
            $this->sendStageNotification($jobApplication);
        }
    }

    /**
     * Send stage change notification
     */
    private function sendStageNotification(JobApplication $jobApplication): void
    {
        // Load the job relationship if not already loaded
        $jobApplication->loadMissing('job');
        
        $newStage = JobStage::find($jobApplication->job_stage_id);
        
        if ($newStage && $newStage->status && $jobApplication->job) {
            $notifiable = new ApplicantNotifiable(
                $jobApplication->email,
                $jobApplication->full_name
            );
            
            try {
                $notifiable->notify(new JobApplicationStageChanged($jobApplication, $newStage));
            } catch (\Exception $e) {
                Log::error('Failed to send job stage notification', [
                    'job_application_id' => $jobApplication->id,
                    'stage_id' => $newStage->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Handle the JobApplication "deleted" event.
     */
    public function deleted(JobApplication $jobApplication): void
    {
        //
    }

    /**
     * Handle the JobApplication "restored" event.
     */
    public function restored(JobApplication $jobApplication): void
    {
        //
    }

    /**
     * Handle the JobApplication "force deleted" event.
     */
    public function forceDeleted(JobApplication $jobApplication): void
    {
        //
    }
}
