<?php

namespace App\Observers;

use App\Events\AfterUserCreation;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the UserVerification "created" event.
     */
    public function created(User $user): void
    {
        AfterUserCreation::dispatch($user);
    }

    /**
     * Handle the UserVerification "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the UserVerification "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the UserVerification "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the UserVerification "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
