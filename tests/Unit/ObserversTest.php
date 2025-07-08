<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ObserversTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sets_status_to_pending_on_task_creation_if_not_provided()
    {
        $task = Task::factory()->create(['status' => null]);
        $this->assertEquals('pending', $task->status);
    }
} 