<?php

namespace Tests\Feature\Api\V1\Ext;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FetchTagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_fetch_tags(): void
    {
        $response = $this->getJson('/api/v1/ext/tags');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_without_ability_cannot_fetch_tags(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['bookmarks:write']);

        $response = $this->getJson('/api/v1/ext/tags');

        $response->assertForbidden();
    }

    public function test_authenticated_user_can_fetch_tags_ordered_by_name(): void
    {
        $user = User::factory()->create();

        Tag::factory()->create(['name' => 'Zebra Tag', 'slug' => 'zebra-tag', 'user_id' => $user->id]);
        Tag::factory()->create(['name' => 'Apple Tag', 'slug' => 'apple-tag', 'user_id' => $user->id]);
        Tag::factory()->create(['name' => 'Mango Tag', 'slug' => 'mango-tag', 'user_id' => $user->id]);

        Sanctum::actingAs($user, ['bookmarks:read']);
        $response = $this->getJson('/api/v1/ext/tags');

        $response->assertOk();

        // The exact json order reflects the orderBy('name') in the controller
        $response->assertExactJson([
            'apple-tag' => 'Apple Tag',
            'mango-tag' => 'Mango Tag',
            'zebra-tag' => 'Zebra Tag',
        ]);
    }
}
