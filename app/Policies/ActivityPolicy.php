<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Activitylog\Models\Activity;

class ActivityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin $user): bool
    {
        return $user->can('view_any_activitylogresource');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Admin $user, Activity $activity): bool
    {
        return $user->can('view_activitylogresource');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin $user): bool
    {
        return $user->can('create_activitylogresource');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin $user, Activity $activity): bool
    {
        return $user->can('update_activitylogresource');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin $user, Activity $activity): bool
    {
        return $user->can('delete_activitylogresource');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(Admin $user): bool
    {
        return $user->can('delete_any_activitylogresource');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(Admin $user, Activity $activity): bool
    {
        return $user->can('force_delete_activitylogresource');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(Admin $user): bool
    {
        return $user->can('force_delete_any_activitylogresource');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(Admin $user, Activity $activity): bool
    {
        return $user->can('restore_activitylogresource');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(Admin $user): bool
    {
        return $user->can('restore_any_activitylogresource');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(Admin $user, Activity $activity): bool
    {
        return $user->can('replicate_activitylogresource');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(Admin $user): bool
    {
        return $user->can('reorder_activitylogresource');
    }
}
