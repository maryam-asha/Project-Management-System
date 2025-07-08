<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_formats_due_date_correctly()
    {
        $project = Project::factory()->create(['due_date' => '2025-12-31']);
        $this->assertEquals('31/12/2025', $project->formatted_due_date);
    }

    /** @test */
    public function it_sanitizes_description()
    {
        $project = Project::factory()->create(['description' => '<b>desc</b>']);
        $this->assertEquals('desc', $project->description);
    }

    /** @test */
    public function it_filters_active_projects()
    {
        Project::factory()->create(['status' => 'active']);
        Project::factory()->create(['status' => 'pending']);
        $this->assertCount(1, Project::active()->get());
    }
} 