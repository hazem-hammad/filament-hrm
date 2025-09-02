<?php

namespace App\Mail;

use App\Models\JobApplication;
use App\Models\JobStage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobStageUpdateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public JobApplication $jobApplication,
        public JobStage $newStage
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->jobApplication->email,
            subject: 'Update on Your Application for ' . $this->jobApplication->job->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.job-stage-update',
            with: [
                'jobApplication' => $this->jobApplication,
                'newStage' => $this->newStage,
                'customContent' => $this->getCustomContent(),
            ]
        );
    }

    /**
     * Get custom email content with placeholders replaced.
     */
    private function getCustomContent(): string
    {
        $emailContent = $this->newStage->email_template;

        if ($emailContent) {
            return str_replace(
                ['{first_name}', '{last_name}', '{job_title}', '{stage_name}'],
                [
                    $this->jobApplication->first_name,
                    $this->jobApplication->last_name,
                    $this->jobApplication->job->title,
                    $this->newStage->name
                ],
                $emailContent
            );
        }

        return '';
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
