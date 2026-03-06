<?php

namespace App\Managers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OpenRouterManager
{
    /**
     * Get a list of available models from OpenRouter.
     *
     * @return array<string, string> Associative array of model ID => model name
     */
    public function getModels(): array
    {
        return Cache::remember('openrouter_models', now()->addHours(6), function () {
            $response = Http::get('https://openrouter.ai/api/v1/models');

            if ($response->failed()) {
                return [];
            }

            $models = $response->json('data', []);

            $result = [];
            foreach ($models as $model) {
                $result[$model['id']] = $model['name'];
            }

            asort($result);

            return $result;
        });
    }
}
