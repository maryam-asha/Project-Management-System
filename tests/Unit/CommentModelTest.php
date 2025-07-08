<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_sanitizes_content()
    {
        $comment = Comment::factory()->create(['content' => '<img src=x onerror=alert(1)>Hello']);
        $this->assertEquals('Hello', $comment->content);
    }
} 