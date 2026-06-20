<?php

namespace Tests\Feature\Users\Categories;

use App\Models\Setting;
use App\Models\User;
use App\Services\CategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCategoryPerPageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->post(route('categories.per-page'), ['per_page' => 25]);

        $response->assertRedirect(route('login'));
    }

    public function test_it_creates_setting_when_none_exists(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.per-page'), ['per_page' => 50]);

        $response->assertRedirect();
        $this->assertDatabaseHas('settings', [
            'user_id' => $user->id,
            'setting' => CategoryService::PER_PAGE_SETTING,
            'value' => '50',
        ]);
    }

    public function test_it_updates_existing_setting(): void
    {
        $user = User::factory()->create();
        Setting::factory()->create([
            'user_id' => $user->id,
            'setting' => CategoryService::PER_PAGE_SETTING,
            'value' => '25',
        ]);

        $response = $this->actingAs($user)->post(route('categories.per-page'), ['per_page' => 100]);

        $response->assertRedirect();
        $this->assertDatabaseHas('settings', [
            'user_id' => $user->id,
            'setting' => CategoryService::PER_PAGE_SETTING,
            'value' => '100',
        ]);
        $this->assertDatabaseCount('settings', 1);
    }

    public function test_invalid_per_page_is_coerced_to_default(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.per-page'), ['per_page' => 999]);

        $response->assertRedirect();
        $this->assertDatabaseHas('settings', [
            'user_id' => $user->id,
            'setting' => CategoryService::PER_PAGE_SETTING,
            'value' => '25',
        ]);
    }

    public function test_all_valid_presets_are_accepted(): void
    {
        $user = User::factory()->create();
        $service = new CategoryService();

        foreach ($service->getPaginationPresets() as $preset) {
            $this->actingAs($user)->post(route('categories.per-page'), ['per_page' => $preset]);

            $this->assertDatabaseHas('settings', [
                'user_id' => $user->id,
                'setting' => CategoryService::PER_PAGE_SETTING,
                'value' => (string) $preset,
            ]);
        }
    }

    public function test_it_does_not_affect_other_users_settings(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Setting::factory()->create([
            'user_id' => $other->id,
            'setting' => CategoryService::PER_PAGE_SETTING,
            'value' => '50',
        ]);

        $this->actingAs($user)->post(route('categories.per-page'), ['per_page' => 100]);

        $this->assertDatabaseHas('settings', [
            'user_id' => $other->id,
            'setting' => CategoryService::PER_PAGE_SETTING,
            'value' => '50',
        ]);
    }
}
