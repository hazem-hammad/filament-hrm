<?php

namespace App\Listeners;

use App\Enum\NotificationReceiversType;
use App\Events\AfterNotificationCreation;
use App\Notifications\GlobalNotification;
use App\Services\User\Auth\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class SendGlobalNotificationListener
{
    const CHUNK = 100;

    /**
     * Create the event listener.
     */
    public function __construct(public UserService $userService) {}

    /**
     * Handle the event.
     */
    public function handle(AfterNotificationCreation $event): void
    {
        try {
            $data = $event->data;

            $callback = function (Collection $users) use ($data) {
                $this->notifyUsers($users, $data);
            };
            $this->userService->getActiveAndHasDeviceTokenUsers(self::CHUNK, $callback);

            Log::info('Notifications sent successfully.');
        } catch (\Exception $e) {
            Log::error('Listener error: ' . $e->getMessage());
        }
    }

    private function notifyUsers(Collection $users, array $data): void
    {
        foreach ($users as $user) {
            Log::info('We are in listeners user', [$user]);
            $user->notify(new GlobalNotification($data));
        }
    }
}
