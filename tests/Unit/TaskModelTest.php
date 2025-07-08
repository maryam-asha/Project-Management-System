<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_formats_due_date_correctly()
    {
        $task = Task::factory()->create(['due_date' => '2025-12-31']);
        $this->assertEquals('31/12/2025', $task->formatted_due_date);
    }

    /** @test */
    public function it_sanitizes_description()
    {
        $task = Task::factory()->create(['description' => '<script>alert(1)</script>Clean']);
        $this->assertEquals('Clean', $task->description);
    }

    /** @test */
    public function it_filters_overdue_tasks()
    {
        Task::factory()->create(['status' => 'overdue']);
        Task::factory()->create(['status' => 'completed']);
        $this->assertCount(1, Task::overdue()->get());
    }

    /** @test */
    public function it_filters_completed_tasks()
    {
        Task::factory()->create(['status' => 'completed']);
        Task::factory()->create(['status' => 'pending']);
        $this->assertCount(1, Task::completed()->get());
    }
} 