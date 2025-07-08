@component('mail::message')
    # New Task Assigned

    Hello {{ $task->assignedUser->name ?? 'User' }},

    You have been assigned a new task: **{{ $task->name }}**

    @isset($task->description)
        **Description:**
        {{ $task->description }}
    @endisset

    **Due Date:** {{ $task->due_date }}

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
