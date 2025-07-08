<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function viewAny(User $user)
    {
        return $user->teams()->exists()
            ? Response::allow()
            : Response::deny('You are not allowed to view comments');
    }

    public function view(User $user, Comment $comment)
    {
        $commentable = $comment->commentable;
        if ($commentable instanceof Task) {
            $team = $commentable->project ? $commentable->project->team : null;
        } elseif ($commentable instanceof Project) {
            $team = $commentable->team;
        } else {
            $team = null;
        }
        return $team && $team->hasUser($user)
            ? Response::allow()
            : Response::deny('You are not allowed to view this comment');
    }

    public function create(User $user)
    {
        return $user->teams()->exists()
            ? Response::allow()
            : Response::deny('You are not allowed to create comments');
    }

    public function update(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id
            ? Response::allow()
            : Response::deny('You are not allowed to update this comment');
    }

    public function delete(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id
            ? Response::allow()
            : Response::deny('You are not allowed to delete this comment');
    }

    public function restore(User $user, Comment $comment)
    {
        return false;
    }

    public function forceDelete(User $user, Comment $comment)
    {
        return false;
    }
}
