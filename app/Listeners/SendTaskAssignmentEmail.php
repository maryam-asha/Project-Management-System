<?php

namespace App\Listeners;

use App\Events\TaskAssigned;
use App\Mail\TaskAssignedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTaskAssignmentEmail implements ShouldQueue
{
    public function handle(TaskAssigned $event)
    {
        $task = $event->task;
        if ($task->assignedUser && $task->assignedUser->email) {
            Mail::to($task->assignedUser->email)->queue(new TaskAssignedMail($task));
        }
    }
}
