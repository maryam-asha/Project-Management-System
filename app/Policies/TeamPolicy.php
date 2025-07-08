<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TeamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        // Can view any if user is a member of any team
        return $user->teams()->exists()
            ? Response::allow()
            : Response::deny('You are not allowed to view teams');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team)
    {
        // Only team members can view
        return $team->hasUser($user)
            ? Response::allow()
            : Response::deny('You are not allowed to view this team');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        // Only team owner or admin can create
        return $user->hasRole('admin')
            ? Response::allow()
            : Response::deny('You are not allowed to create a team');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team)
    {
        // Only team owner or admin can update
        return $user->id === $team->owner_id
            || $user->hasRole('admin', $team->id)
            ? Response::allow()
            : Response::deny('You are not allowed to update this team');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team)
    {
        // Only team owner or admin can delete
        return $user->id === $team->owner_id
            || $user->hasRole('admin', $team->id)
            ? Response::allow()
            : Response::deny('You are not allowed to delete this team');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Team $team): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Team $team): bool
    {
        return false;
    }
}
