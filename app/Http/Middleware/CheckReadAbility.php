<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckReadAbility
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->currentAccessToken()) {
            abort(401, 'Unauthenticated.');
        }

        if (! $request->user()->tokenCan('read') && ! $request->user()->tokenCan('bookmarks:read') && ! $request->user()->tokenCan('*')) {
            abort(403, 'Access token used does not have read permission.');
        }

        return $next($request);
    }
}
