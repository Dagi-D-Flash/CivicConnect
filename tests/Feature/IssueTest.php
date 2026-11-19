<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_issue(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post('/api/issues', [
            'title' => 'Test Issue',
            'description' => 'This is a test issue description.',
            'category_id' => $category->id,
            'location' => 'Test Location',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('issues', [
            'title' => 'Test Issue',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_view_issue(): void
    {
        $issue = Issue::factory()->create();

        $response = $this->get('/issues/' . $issue->id);

        $response->assertStatus(200);
    }

    public function test_user_can_update_own_issue(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put('/api/issues/' . $issue->id, [
            'title' => 'Updated Issue',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('issues', [
            'id' => $issue->id,
            'title' => 'Updated Issue',
        ]);
    }

    public function test_user_cannot_update_others_issue(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $issue = Issue::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->put('/api/issues/' . $issue->id, [
            'title' => 'Updated Issue',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_issue(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete('/api/issues/' . $issue->id);

        $response->assertStatus(200);
        $this->assertSoftDeleted('issues', ['id' => $issue->id]);
    }
}
