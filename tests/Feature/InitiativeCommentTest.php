<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Initiative;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InitiativeCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_post_comments(): void
    {
        $initiative = Initiative::factory()->create();

        $this->post(route('initiatives.comments.store', $initiative), [
            'body' => 'Nice initiative!',
        ])->assertRedirect(route('login'));
    }

    /**
     * @throws \JsonException
     */
    public function test_authenticated_user_can_post_comment(): void
    {
        $user = User::factory()->create();
        $initiative = Initiative::factory()->create();

        $response = $this->actingAs($user)->post(route('initiatives.comments.store', $initiative), [
            'body' => 'I support this idea.',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('initiatives.show', $initiative));

        $this->assertDatabaseHas('comments', [
            'initiative_id' => $initiative->id,
            'user_id' => $user->id,
            'body' => 'I support this idea.',
        ]);
    }

    public function test_comment_body_is_required(): void
    {
        $user = User::factory()->create();
        $initiative = Initiative::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('initiatives.show', $initiative))
            ->post(route('initiatives.comments.store', $initiative), []);

        $response
            ->assertSessionHasErrors('body')
            ->assertRedirect(route('initiatives.show', $initiative));

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_initiative_show_includes_comments(): void
    {
        $initiative = Initiative::factory()->create();
        $comment = Comment::factory()->for($initiative)->create();

        $response = $this->actingAs(User::factory()->create())
            ->get(route('initiatives.show', $initiative));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Initiatives/Show')
            ->has('initiative.comments', 1)
            ->where('initiative.comments.0.id', $comment->id)
            ->where('initiative.comments.0.body', $comment->body)
        );
    }

    public function test_user_can_delete_own_comment(): void
    {
        $user = User::factory()->create();
        $initiative = Initiative::factory()->create();
        $comment = Comment::factory()->for($initiative)->for($user)->create();

        $response = $this->actingAs($user)->delete(
            route('initiatives.comments.destroy', [$initiative, $comment])
        );

        $response->assertRedirect(route('initiatives.show', $initiative));
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_user_cannot_delete_another_users_comment(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $initiative = Initiative::factory()->create();
        $comment = Comment::factory()->for($initiative)->for($owner)->create();

        $response = $this->actingAs($otherUser)->delete(
            route('initiatives.comments.destroy', [$initiative, $comment])
        );

        $response->assertForbidden();
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_comment_must_belong_to_initiative(): void
    {
        $user = User::factory()->create();
        $initiative = Initiative::factory()->create();
        $otherInitiative = Initiative::factory()->create();
        $comment = Comment::factory()->for($otherInitiative)->for($user)->create();

        $response = $this->actingAs($user)->delete(
            route('initiatives.comments.destroy', [$initiative, $comment])
        );

        $response->assertNotFound();
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }
}
