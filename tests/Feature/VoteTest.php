<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_vote_on_issue(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->create();

        $response = $this->actingAs($user)->post('/api/issues/' . $issue->id . '/vote');

        $response->assertStatus(200);
        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'votable_type' => 'App\\Models\\Issue',
            'votable_id' => $issue->id,
        ]);
    }

    public function test_user_can_remove_vote(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->create();
        
        // First vote
        $this->actingAs($user)->post('/api/issues/' . $issue->id . '/vote');

        // Remove vote
        $response = $this->actingAs($user)->delete('/api/issues/' . $issue->id . '/vote');

        $response->assertStatus(200);
        $this->assertDatabaseMissing('votes', [
            'user_id' => $user->id,
            'votable_type' => 'App\\Models\\Issue',
            'votable_id' => $issue->id,
        ]);
    }

    public function test_user_cannot_vote_twice(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->create();

        // First vote
        $this->actingAs($user)->post('/api/issues/' . $issue->id . '/vote');

        // Try to vote again
        $response = $this->actingAs($user)->post('/api/issues/' . $issue->id . '/vote');

        $response->assertStatus(422);
    }

    public function test_guest_can_vote_with_session(): void
    {
        $issue = Issue::factory()->create();

        $response = $this->post('/api/issues/' . $issue->id . '/vote-guest');

        $response->assertStatus(200);
        $response->assertSessionHas('voted_issues');
    }
}
