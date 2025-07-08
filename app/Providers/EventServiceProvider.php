<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\TaskAssigned;
use App\Events\CommentCreated;
use App\Listeners\SendTaskAssignmentEmail;
use App\Listeners\CreateTaskAssignmentNotification;
use App\Listeners\NotifyProjectUsersOfComment;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TaskAssigned::class => [
            SendTaskAssignmentEmail::class,
            CreateTaskAssignmentNotification::class,
        ],
        CommentCreated::class => [
            NotifyProjectUsersOfComment::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
} 