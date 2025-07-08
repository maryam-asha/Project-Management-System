<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyProjectUsersOfComment implements ShouldQueue
{
    public function handle(CommentCreated $event)
    {
        $comment = $event->comment;
        $project = $comment->commentable_type === 'App\\Models\\Project'
            ? $comment->commentable
            : ($comment->commentable->project ?? null);

        if ($project) {
            foreach ($project->users as $user) {
                if ($user->id !== $comment->user_id) {
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'type' => 'comment_added',
                        'data' => json_encode(['comment_id' => $comment->id, 'content' => $comment->content]),
                    ]);
                }
            }
        }
    }
}
