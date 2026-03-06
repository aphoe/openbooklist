<?php

namespace App\Http\Controllers\Users\Settings;

use App\Enums\AccessTokenAbility;
use App\Http\Controllers\Controller;
use App\Managers\OpenRouterManager;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, OpenRouterManager $openRouterManager): Response
    {
        return Inertia::render('Users/Settings/Index', [
            'hasOpenRouterKey' => ! empty(config('project.openrouter_key')),
            'tab' => $request->query('tab', 'general'),
            'aiModels' => Inertia::defer(fn () => collect($openRouterManager->getModels())->map(fn ($name, $id) => ['id' => $id, 'name' => $name])->values()->toArray()),
            'availableAbilities' => AccessTokenAbility::labelArray(),
            'tokens' => $request->user()->tokens()->orderByDesc('created_at')->get()->map(fn ($token) => [
                'id' => $token->id,
                'name' => $token->name,
                'abilities' => collect($token->abilities)->map(fn ($ability) => $ability === '*' ? 'All Abilities' : AccessTokenAbility::tryFrom($ability)?->label() ?? $ability)->toArray(),
                'last_used_at' => $token->last_used_at?->diffForHumans(),
                'expires_at' => $token->expires_at ? $token->expires_at->format('M d, Y') : 'Never',
                'created_at' => $token->created_at->format('M d, Y'),
            ]),
        ]);
    }
}
