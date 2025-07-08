<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any projects.
     */
    public function viewAny(User $user)
    {
        // Can view any if user is a member of any team
        return $user->teams()->exists()
            ? Response::allow()
            : Response::deny('You are not allowed to view projects');
    }

    /**
     * Determine whether the user can view the project.
     */
    public function view(User $user, Project $project)
    {
        // Only team members can view
        return $project->team && $project->team->hasUser($user)
            ? Response::allow()
            : Response::deny('You are not allowed to view this project');
    }

    /**
     * Determine whether the user can create a project in a team.
     *
     * @param User $user
     * @param Team $team
     * @return bool
     */
    public function create(User $user,Team $team)
    {
        // Only team owner, admin, or project_manager can create
        return $user->id === $team->owner_id
            || $user->hasRole('admin', $team->id)
            || $user->hasRole('project_manager', $team->id)
            ? Response::allow()
            : Response::deny('You are not allowed to create this project');
    }

    /**
     * Determine whether the user can update the project.
     */
    public function update(User $user, Project $project)
    {
        // Only team owner, admin, or project_manager can update
        $team = $project->team;
        return $team && (
            $user->id === $team->owner_id
            || $user->hasRole('admin', $team->id)
            || $user->hasRole('project_manager', $team->id)
        ) ? Response::allow()
            : Response::deny('You are not allowed to update this project');
    }

    /**
     * Determine whether the user can delete the project.
     */
    public function delete(User $user, Project $project)
    {
        // Only team owner, admin, or project_manager can delete
        $team = $project->team;
        return $team && (
            $user->id === $team->owner_id
            || $user->hasRole('admin', $team->id)
            || $user->hasRole('project_manager', $team->id)
        ) ? Response::allow()
            : Response::deny('You are not allowed to delete this project');
    }

    /**
     * Determine whether the user can restore the project.
     */
    public function restore(User $user, Project $project)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the project.
     */
    public function forceDelete(User $user, Project $project)
    {
        return false;
    }
}
