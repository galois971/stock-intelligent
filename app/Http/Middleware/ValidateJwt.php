<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class ValidateJwt
{
    public function handle($request, Closure $next)
    {
        $header = $request->header('Authorization');
        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Token manquant'], 401);
        }

        $token = substr($header, 7);
        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token invalide: ' . $e->getMessage()], 401);
        }

        $user = User::find($decoded->sub ?? null);
        if (! $user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 401);
        }

        // Attach user to request for controllers
        $request->merge(['user' => $user]);
        $request->user = $user;

        return $next($request);
    }
}
