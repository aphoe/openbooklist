<?php

namespace Tests\Feature\Repositories;

use App\Models\Setting;
use App\Models\User;
use App\Repositories\SettingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_setting(): void
    {
        $user = User::factory()->create();

        $repo = new SettingRepository;
        $setting = $repo->create(
            user: $user,
            setting: 'theme',
            value: 'dark'
        );

        $this->assertInstanceOf(Setting::class, $setting);
        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'user_id' => $user->id,
            'setting' => 'theme',
            'value' => 'dark',
        ]);
    }

    public function test_it_updates_a_setting(): void
    {
        $user = User::factory()->create();
        $setting = Setting::factory()->create(['user_id' => $user->id, 'setting' => 'theme']);

        $repo = new SettingRepository;
        $repo->update(
            setting: $setting,
            name: 'theme',
            value: 'light'
        );

        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'setting' => 'theme',
            'value' => 'light',
        ]);
    }

    public function test_it_updates_setting_name_and_value(): void
    {
        $user = User::factory()->create();
        $setting = Setting::factory()->create(['user_id' => $user->id, 'setting' => 'theme']);

        $repo = new SettingRepository;
        $repo->update(
            setting: $setting,
            name: 'color_mode',
            value: 'light'
        );

        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'setting' => 'color_mode',
            'value' => 'light',
        ]);
    }

    public function test_it_retrieves_a_setting_by_user_and_name(): void
    {
        $user = User::factory()->create();
        Setting::factory()->create(['user_id' => $user->id, 'setting' => 'theme', 'value' => 'dark']);
        Setting::factory()->create(['user_id' => $user->id, 'setting' => 'language', 'value' => 'en']);

        $repo = new SettingRepository;
        $setting = $repo->get($user, 'theme');

        $this->assertNotNull($setting);
        $this->assertInstanceOf(Setting::class, $setting);
        $this->assertEquals('theme', $setting->setting);
        $this->assertEquals('dark', $setting->value);
    }

    public function test_it_returns_null_if_setting_not_found(): void
    {
        $user = User::factory()->create();

        $repo = new SettingRepository;
        $setting = $repo->get($user, 'nonexistent');

        $this->assertNull($setting);
    }

    public function test_it_deletes_a_setting(): void
    {
        $user = User::factory()->create();
        $setting = Setting::factory()->create(['user_id' => $user->id]);

        $repo = new SettingRepository;
        $deleted = $repo->delete($setting);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('settings', ['id' => $setting->id]);
    }

    public function test_it_does_not_delete_settings_of_other_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $setting1 = Setting::factory()->create(['user_id' => $user1->id, 'setting' => 'theme']);
        $setting2 = Setting::factory()->create(['user_id' => $user2->id, 'setting' => 'theme']);

        $repo = new SettingRepository;
        $repo->delete($setting1);

        $this->assertDatabaseMissing('settings', ['id' => $setting1->id]);
        $this->assertDatabaseHas('settings', ['id' => $setting2->id]);
    }
}
