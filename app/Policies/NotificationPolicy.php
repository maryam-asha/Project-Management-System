<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NotificationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user !== null
            ? Response::allow()
            : Response::deny('You are not allowed to view notifications');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Notification $notification)
    {
        return $notification->user_id === $user->id
            ? Response::allow()
            : Response::deny('You are not allowed to view this notification');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user !== null
            ? Response::allow()
            : Response::deny('You are not allowed to create notifications');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Notification $notification)
    {
        return $notification->user_id === $user->id
            ? Response::allow()
            : Response::deny('You are not allowed to update this notification');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Notification $notification)
    {
        return $notification->user_id === $user->id
            ? Response::allow()
            : Response::deny('You are not allowed to delete this notification');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Notification $notification)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Notification $notification)
    {
        return false;
    }
} 