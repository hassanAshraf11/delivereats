<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin,restaurant_owner')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Required role: ' . implode(' or ', $roles)], 403);
            }
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
