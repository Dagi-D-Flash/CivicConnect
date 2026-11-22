<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_comment(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->create();

        $response = $this->actingAs($user)->post('/api/comments', [
            'content' => 'This is a test comment.',
            'commentable_type' => 'App\\Models\\Issue',
            'commentable_id' => $issue->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', [
            'content' => 'This is a test comment.',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_reply_to_comment(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->create();
        $parentComment = Comment::factory()->create([
            'commentable_type' => 'App\\Models\\Issue',
            'commentable_id' => $issue->id,
        ]);

        $response = $this->actingAs($user)->post('/api/comments', [
            'content' => 'This is a reply.',
            'commentable_type' => 'App\\Models\\Issue',
            'commentable_id' => $issue->id,
            'parent_id' => $parentComment->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', [
            'content' => 'This is a reply.',
            'parent_id' => $parentComment->id,
        ]);
    }

    public function test_user_can_update_own_comment(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put('/api/comments/' . $comment->id, [
            'content' => 'Updated comment.',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated comment.',
        ]);
    }

    public function test_user_can_delete_own_comment(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete('/api/comments/' . $comment->id);

        $response->assertStatus(200);
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }
}
