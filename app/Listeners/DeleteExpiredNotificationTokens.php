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
        $report = Arr::get($event->data, 'report');

        $target = $report->target();

        $event->notifiable->notificationTokens()
            ->where('device_token', $target->value())
            ->each(function ($token) {
                $token->update(['device_token' => null]);
            });
    }
}
