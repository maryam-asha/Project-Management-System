<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        // Can view any if user is a member of any team
        return $user->teams()->exists()
            ? Response::allow()
            : Response::deny('You are not allowed to view tasks');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task)
    {
        // Only team members can view
        $team = $task->project ? $task->project->team : null;
        return $team && $team->hasUser($user)
            ? Response::allow()
            : Response::deny('You are not allowed to view this task');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Project $project)
    {
        $team = $project->team;
        return $team && (
            $user->id === $team->owner_id
            || $user->hasRole('admin', $team->id)
            || $user->hasRole('project_manager', $team->id)
        ) ? Response::allow()
          : Response::deny('You are not allowed to create tasks for this project');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task)
    {
        $team = $task->project ? $task->project->team : null;
        return $team && (
            $user->id === $team->owner_id
            || $user->hasRole('admin', $team->id)
            || $user->hasRole('project_manager', $team->id)
            || $task->assigned_to_user_id === $user->id
        ) ? Response::allow()
          : Response::deny('You are not allowed to update this task');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task)
    {
        $team = $task->project ? $task->project->team : null;
        return $team && (
            $user->id === $team->owner_id
            || $user->hasRole('admin', $team->id)
            || $user->hasRole('project_manager', $team->id)
            || $task->assigned_to_user_id === $user->id
        ) ? Response::allow()
          : Response::deny('You are not allowed to delete this task');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task)
    {
        return false;
    }
}
