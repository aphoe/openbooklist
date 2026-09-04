<?php

namespace Tests\Feature\Api\V1\App\Bookmarks;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateBookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_github_prefix_is_stripped_from_title_on_update(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id, 'title' => 'Old Title']);

        Sanctum::actingAs($user, ['bookmarks:write']);

        $response = $this->putJson("/api/v1/app/bookmarks/{$bookmark->id}", [
            'url' => 'https://github.com/vuejs/core',
            'title' => 'GitHub - vuejs/core: The Vue.js framework',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'title' => 'vuejs/core: The Vue.js framework',
        ]);
    }

    public function test_title_is_untouched_for_non_github_urls_on_update(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id, 'title' => 'Old Title']);

        Sanctum::actingAs($user, ['bookmarks:write']);

        $response = $this->putJson("/api/v1/app/bookmarks/{$bookmark->id}", [
            'url' => 'https://example.com/article',
            'title' => 'GitHub - a literal title on another site',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'title' => 'GitHub - a literal title on another site',
        ]);
    }
}
