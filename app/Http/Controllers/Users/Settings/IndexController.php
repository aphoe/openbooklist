<?php

namespace App\Http\Controllers\Users\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Users/Settings/Index', [
            'hasOpenRouterKey' => ! empty(config('project.openrouter_key')),
            'tab' => $request->query('tab', 'general'),
            'tokens' => $request->user()->tokens()->orderByDesc('created_at')->get()->map(fn ($token) => [
                'id' => $token->id,
                'name' => $token->name,
                'abilities' => $token->abilities,
                'last_used_at' => $token->last_used_at?->diffForHumans(),
                'created_at' => $token->created_at->format('M d, Y'),
            ]),
        ]);
    }
}
