<?php
namespace App\Observers;

use App\Models\Task;
use App\Events\TaskAssigned;

class TaskObserver
{
    public function creating(Task $task)
    {
        if (!$task->status) {
            $task->status = 'pending';
        }
    }

    public function created(Task $task)
    {
        if ($task->assigned_to_user_id) {
            event(new TaskAssigned($task));
        }
    }

    public function updated(Task $task)
    {
        if ($task->isDirty('status') && $task->status === 'completed') {
           
        }
        if ($task->isDirty('assigned_to_user_id')) {
            event(new TaskAssigned($task));
        }
    }
} 