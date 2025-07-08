<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use App\Models\Comment;
use Illuminate\Auth\Access\Response;

class AttachmentPolicy
{
    public function viewAny(User $user)
    {
        return $user->teams()->exists()
            ? Response::allow()
            : Response::deny('You are not allowed to view attachments');
    }

    public function view(User $user, Attachment $attachment)
    {
        $attachable = $attachment->attachable;
        if ($attachable instanceof Task) {
            $team = $attachable->project ? $attachable->project->team : null;
        } elseif ($attachable instanceof Project) {
            $team = $attachable->team;
        } elseif ($attachable instanceof Comment) {
            if ($attachable->commentable instanceof Task) {
                $team = $attachable->commentable->project ? $attachable->commentable->project->team : null;
            } elseif ($attachable->commentable instanceof Project) {
                $team = $attachable->commentable->team;
            } else {
                $team = null;
            }
        } else {
            $team = null;
        }
        return $team && $team->hasUser($user)
            ? Response::allow()
            : Response::deny('You are not allowed to view this attachment');
    }

    public function create(User $user, $attachable)
    {
        if ($attachable instanceof Task) {
            $team = $attachable->project ? $attachable->project->team : null;
        } elseif ($attachable instanceof Project) {
            $team = $attachable->team;
        } elseif ($attachable instanceof Comment) {
            if ($attachable->commentable instanceof Task) {
                $team = $attachable->commentable->project ? $attachable->commentable->project->team : null;
            } elseif ($attachable->commentable instanceof Project) {
                $team = $attachable->commentable->team;
            } else {
                $team = null;
            }
        } else {
            $team = null;
        }
        return $team && $team->hasUser($user)
            ? Response::allow()
            : Response::deny('You are not allowed to upload attachments');
    }

    public function delete(User $user, Attachment $attachment)
    {
        return $user->id === $attachment->user_id || $user->hasRole('admin')
            ? Response::allow()
            : Response::deny('You are not allowed to delete this attachment');
    }
} 