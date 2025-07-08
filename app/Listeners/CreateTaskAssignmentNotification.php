<?php

namespace App\Listeners;

use App\Events\TaskAssigned;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateTaskAssignmentNotification implements ShouldQueue
{
    public function handle(TaskAssigned $event)
    {
        $task = $event->task;
        \App\Models\Notification::create([
            'user_id' => $task->assigned_to_user_id,
            'type' => 'task_assigned',
            'data' => json_encode(['task_id' => $task->id, 'name' => $task->name]),
        ]);
    }
}
