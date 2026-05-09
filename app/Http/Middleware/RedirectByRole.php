<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectByRole
{
    /**
     * Redirect users to their role-appropriate dashboard if they try to
     * access pages meant for a different role.
     *
     * Usage: middleware('redirect.role:customer') — only customers can access
     */
    public function handle(Request $request, Closure $next, string ...$allowedRoles): Response
    {
        if (Auth::check()) {
            $role = Auth::user()->role;

            if (!in_array($role, $allowedRoles)) {
                return match ($role) {
                    'admin' => redirect()->route('admin.tower'),
                    'restaurant_owner' => redirect()->route('restaurant.dashboard'),
                    'rider' => redirect()->route('rider.dashboard'),
                    default => redirect()->route('browse'),
                };
            }
        }

        return $next($request);
    }
}
