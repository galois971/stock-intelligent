<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: role:admin,manager
     */
    public function handle($request, Closure $next, $roles)
    {
        $user = $request->user() ?? null;
        if (! $user) {
            // For web requests redirect to login, for API return JSON
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            return redirect()->route('login');
        }

        $allowed = array_map('trim', explode(',', $roles));

        foreach ($allowed as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden (role)'], 403);
        }

        abort(403, 'Forbidden (role)');
    }
}
