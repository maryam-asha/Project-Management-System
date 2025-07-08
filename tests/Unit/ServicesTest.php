<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TaskService;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServicesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function task_service_create_sets_status_to_pending()
    {
        $service = new TaskService();
        $data = [
            'name' => 'Test Task',
            'description' => 'desc',
            'priority' => 'low',
            'due_date' => now()->addDay()->toDateString(),
            'project_id' => 1,
        ];
        $task = $service->create($data, 1);
        $this->assertEquals('pending', $task->status);
        $this->assertDatabaseHas('tasks', ['name' => 'Test Task']);
    }
} 