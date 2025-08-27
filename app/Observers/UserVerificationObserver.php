<?php

namespace App\Observers;

use App\Events\AfterCreateUserVerification;
use App\Models\UserVerification;

class UserVerificationObserver
{
    /**
     * Handle the UserVerification "created" event.
     */
    public function created(UserVerification $userVerification): void
    {
        AfterCreateUserVerification::dispatch($userVerification);
    }

    /**
     * Handle the UserVerification "updated" event.
     */
    public function updated(UserVerification $userVerification): void
    {
        //
    }

    /**
     * Handle the UserVerification "deleted" event.
     */
    public function deleted(UserVerification $userVerification): void
    {
        //
    }

    /**
     * Handle the UserVerification "restored" event.
     */
    public function restored(UserVerification $userVerification): void
    {
        //
    }

    /**
     * Handle the UserVerification "force deleted" event.
     */
    public function forceDeleted(UserVerification $userVerification): void
    {
        //
    }
}
