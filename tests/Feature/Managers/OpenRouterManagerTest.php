<?php

namespace Tests\Feature\Managers;

use App\Managers\OpenRouterManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenRouterManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_gets_models_from_openrouter(): void
    {
        Http::fake([
            'https://openrouter.ai/api/v1/models' => Http::response([
                'data' => [
                    ['id' => 'model-b', 'name' => 'Model B'],
                    ['id' => 'model-a', 'name' => 'Model A'],
                ],
            ], 200),
        ]);

        $manager = new OpenRouterManager;
        $models = $manager->getModels();

        // Must be sorted alphabetically by value
        $this->assertEquals([
            'model-a' => 'Model A',
            'model-b' => 'Model B',
        ], $models);

        $this->assertTrue(Cache::has('openrouter_models'));
    }

    public function test_it_returns_empty_array_if_models_fetch_fails(): void
    {
        Http::fake([
            'https://openrouter.ai/api/v1/models' => Http::response(null, 500),
        ]);

        $manager = new OpenRouterManager;
        $models = $manager->getModels();

        $this->assertEquals([], $models);
    }

    public function test_it_generates_description(): void
    {
        Config::set('project.openrouter_key', 'valid-key');

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'This is a concise AI summary.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $manager = new OpenRouterManager;
        $description = $manager->generateDescription('<html><body><p>Many words here...</p></body></html>', 'test-model');

        $this->assertEquals('This is a concise AI summary.', $description);
    }

    public function test_it_returns_null_if_generate_description_fails(): void
    {
        Config::set('project.openrouter_key', 'valid-key');

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response(null, 500),
        ]);

        $manager = new OpenRouterManager;
        $description = $manager->generateDescription('<html><body><p>More words</p></body></html>', 'test-model');

        $this->assertNull($description);
    }

    public function test_it_returns_null_if_api_key_is_missing(): void
    {
        Config::set('project.openrouter_key', null);

        $manager = new OpenRouterManager;
        $description = $manager->generateDescription('<html><body><p>More words</p></body></html>', 'test-model');

        $this->assertNull($description);
    }
}
