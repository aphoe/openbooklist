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

    /**
     * Generate a short description for a web page using the OpenRouter chat completions API.
     *
     * @param  string  $htmlBody  The raw HTML body of the page.
     * @param  string  $model  The model ID to use for generation.
     */
    public function generateDescription(string $htmlBody, string $model): ?string
    {
        $apiKey = config('project.openrouter_key');

        if (empty($apiKey)) {
            return null;
        }

        // Strip HTML tags and decode entities to get plain text
        $plainText = html_entity_decode(strip_tags($htmlBody), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plainText = preg_replace('/\s+/', ' ', trim($plainText));

        // Limit the input to avoid excessive token usage
        $plainText = mb_substr($plainText, 0, 4000);

        if (empty($plainText)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
            ])->timeout(30)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a concise web page summarizer. Given the text content of a web page, write a clear, informative summary that captures the main purpose or topic of the page. The summary MUST be under 255 characters. Do not include any quotation marks, prefixes like "Summary:" or "This page is about", or meta-commentary. Just output the summary text directly.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $plainText,
                    ],
                ],
                'max_tokens' => 100,
            ]);

            if ($response->failed()) {
                return null;
            }

            $content = $response->json('choices.0.message.content');

            return $content ? mb_substr(trim($content), 0, 255) : null;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}
