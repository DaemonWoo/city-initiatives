<?php

namespace Tests\Feature;

use App\Models\Initiative;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InitiativeVoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_vote_for_initiative(): void
    {
        $initiative = Initiative::factory()->create();

        $response = $this->post(route('initiatives.votes.store', $initiative));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('votes', 0);
    }

    public function test_authenticated_user_can_vote_once(): void
    {
        $user = User::factory()->create();
        $initiative = Initiative::factory()->create();

        $response = $this->actingAs($user)->post(route('initiatives.votes.store', $initiative));

        $response->assertRedirect(route('initiatives.show', $initiative));

        $this->assertDatabaseHas('votes', [
            'initiative_id' => $initiative->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseCount('votes', 1);
    }

    public function test_user_cannot_create_duplicate_vote_for_same_initiative(): void
    {
        $user = User::factory()->create();
        $initiative = Initiative::factory()->create();

        $this->actingAs($user)->post(route('initiatives.votes.store', $initiative));
        $this->actingAs($user)->post(route('initiatives.votes.store', $initiative));

        $this->assertDatabaseCount('votes', 1);
    }

    public function test_user_can_vote_for_different_initiatives(): void
    {
        $user = User::factory()->create();
        $firstInitiative = Initiative::factory()->create();
        $secondInitiative = Initiative::factory()->create();

        $this->actingAs($user)->post(route('initiatives.votes.store', $firstInitiative));
        $this->actingAs($user)->post(route('initiatives.votes.store', $secondInitiative));

        $this->assertDatabaseCount('votes', 2);
    }
}
