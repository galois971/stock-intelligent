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
        $user = $request->user ?? null;
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $allowed = array_map('trim', explode(',', $roles));

        foreach ($allowed as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Forbidden (role)'], 403);
    }
}
