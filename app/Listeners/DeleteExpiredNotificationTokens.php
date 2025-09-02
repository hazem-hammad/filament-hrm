<?php

namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Arr;

class DeleteExpiredNotificationTokens
{
    /**
     * Handle the event.
     */
    public function handle(NotificationFailed $event): void
    {
        // Only handle this for notifiables that have the notificationTokens method
        if (!method_exists($event->notifiable, 'notificationTokens')) {
            return;
        }

        $report = Arr::get($event->data, 'report');
        
        // Check if report and target exist
        if (!$report || !method_exists($report, 'target')) {
            return;
        }

        $target = $report->target();

        if (!$target || !method_exists($target, 'value')) {
            return;
        }

        $event->notifiable->notificationTokens()
            ->where('device_token', $target->value())
            ->each(function ($token) {
                $token->update(['device_token' => null]);
            });
    }
}
