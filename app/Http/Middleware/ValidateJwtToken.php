<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ValidateJwtToken
{
    public function handle($request, Closure $next)
    {
        try {
            // Valida o token JWT
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token inválido ou ausente'], 401);
        }

        // Define o usuário autenticado na requisição
        $request->attributes->add(['user' => $user]);

        return $next($request);
    }
}