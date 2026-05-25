<?php

namespace Tests\Feature;

use App\Models\Initiative;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InitiativeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_initiatives(): void
    {
        $initiative = Initiative::factory()->create();

        $this->get(route('initiatives.index'))->assertRedirect(route('login'));
        $this->get(route('initiatives.create'))->assertRedirect(route('login'));
        $this->get(route('initiatives.show', $initiative))->assertRedirect(route('login'));
        $this->post(route('initiatives.store'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_initiative_pages(): void
    {
        $user = User::factory()->create();
        $initiative = Initiative::factory()->for($user)->create();

        $this->actingAs($user)->get(route('initiatives.index'))->assertOk();
        $this->actingAs($user)->get(route('initiatives.create'))->assertOk();
        $this->actingAs($user)->get(route('initiatives.show', $initiative))->assertOk();
        $this->actingAs($user)->get(route('initiatives.edit', $initiative))->assertOk();
    }

    public function test_user_can_create_initiative(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('initiatives.store'), [
            'title' => 'New park',
            'description' => 'We need more green space downtown.',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('initiatives.index'));

        $this->assertDatabaseHas('initiatives', [
            'user_id' => $user->id,
            'title' => 'New park',
            'description' => 'We need more green space downtown.',
            'image' => null,
        ]);
    }

    public function test_create_initiative_requires_title_and_description(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from(route('initiatives.create'))->post(route('initiatives.store'), []);

        $response
            ->assertSessionHasErrors(['title', 'description'])
            ->assertRedirect(route('initiatives.create'));

        $this->assertDatabaseCount('initiatives', 0);
    }

    public function test_user_can_create_initiative_with_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('initiatives.store'), [
            'title' => 'Bike lanes',
            'description' => 'Safer cycling routes.',
            'image' => UploadedFile::fake()->image('initiative.jpg'),
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('initiatives.index'));

        $initiative = Initiative::query()->first();

        $this->assertNotNull($initiative->image);
        Storage::disk('public')->assertExists($initiative->image);
    }

    public function test_user_can_update_own_initiative(): void
    {
        $user = User::factory()->create();
        $initiative = Initiative::factory()->for($user)->create([
            'title' => 'Old title',
            'description' => 'Old description',
        ]);

        $response = $this->actingAs($user)->put(route('initiatives.update', $initiative), [
            'title' => 'Updated title',
            'description' => 'Updated description',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('initiatives.index'));

        $this->assertDatabaseHas('initiatives', [
            'id' => $initiative->id,
            'title' => 'Updated title',
            'description' => 'Updated description',
        ]);
    }

    public function test_user_can_update_only_title(): void
    {
        $user = User::factory()->create();
        $initiative = Initiative::factory()->for($user)->create([
            'title' => 'Original title',
            'description' => 'Keep this description',
        ]);

        $response = $this->actingAs($user)->put(route('initiatives.update', $initiative), [
            'title' => 'New title only',
        ]);

        $response->assertSessionHasNoErrors();

        $initiative->refresh();

        $this->assertSame('New title only', $initiative->title);
        $this->assertSame('Keep this description', $initiative->description);
    }

    public function test_user_cannot_update_another_users_initiative(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $initiative = Initiative::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->put(route('initiatives.update', $initiative), [
            'title' => 'Hijacked title',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('initiatives', [
            'id' => $initiative->id,
            'title' => $initiative->title,
        ]);
    }

    public function test_updating_image_replaces_old_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $initiative = Initiative::factory()->for($user)->create([
            'image' => 'initiatives/old.jpg',
        ]);
        Storage::disk('public')->put('initiatives/old.jpg', 'old');

        $response = $this->actingAs($user)->put(route('initiatives.update', $initiative), [
            'image' => UploadedFile::fake()->image('new.jpg'),
        ]);

        $response->assertSessionHasNoErrors();

        $initiative->refresh();

        Storage::disk('public')->assertMissing('initiatives/old.jpg');
        Storage::disk('public')->assertExists($initiative->image);
        $this->assertNotSame('initiatives/old.jpg', $initiative->image);
    }

    public function test_user_can_delete_own_initiative(): void
    {
        $user = User::factory()->create();
        $initiative = Initiative::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('initiatives.destroy', $initiative));

        $response
            ->assertRedirect(route('initiatives.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('initiatives', ['id' => $initiative->id]);
    }

    public function test_user_cannot_delete_another_users_initiative(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $initiative = Initiative::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->delete(route('initiatives.destroy', $initiative));

        $response->assertForbidden();
        $this->assertDatabaseHas('initiatives', ['id' => $initiative->id]);
    }

    public function test_deleting_initiative_removes_image_from_storage(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $initiative = Initiative::factory()->for($user)->withImage('initiatives/to-delete.jpg')->create();
        Storage::disk('public')->put('initiatives/to-delete.jpg', 'content');

        $this->actingAs($user)->delete(route('initiatives.destroy', $initiative));

        Storage::disk('public')->assertMissing('initiatives/to-delete.jpg');
    }

    public function test_store_rejects_invalid_image(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from(route('initiatives.create'))->post(route('initiatives.store'), [
            'title' => 'Valid title',
            'description' => 'Valid description',
            'image' => UploadedFile::fake()->create('document.pdf', 100),
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('initiatives', 0);
    }
}
