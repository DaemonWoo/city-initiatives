<?php

namespace Tests\Feature;

use App\Models\Initiative;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InitiativeCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_count_views(): void
    {
        $initiative = Initiative::factory()->create();
        $user = User::factory()->create();
        $initialViews = $initiative->getViewsCountAttribute();

        $this->actingAs($user)->get(route('initiatives.show', $initiative));
        $initiative->refresh();
        $this->assertEquals($initialViews + 1, $initiative->getViewsCountAttribute());
    }

    public function test_it_only_increments_views_once_per_session(): void
    {
        $initiative = Initiative::factory()->create();
        $user = User::factory()->create();
        $initialViews = $initiative->getViewsCountAttribute();

        $this->startSession();
        $this->withSession(['viewed_initiatives' => []]);

        // First view - should increment
        $this->actingAs($user)->get(route('initiatives.show', $initiative));
        $initiative->refresh();
        $this->assertEquals($initialViews + 1, $initiative->getViewsCountAttribute());

        // Second view - should not increment
        $this->actingAs($user)->get(route('initiatives.show', $initiative));
        $initiative->refresh();
        $this->assertEquals($initialViews + 1, $initiative->getViewsCountAttribute());
    }
}
