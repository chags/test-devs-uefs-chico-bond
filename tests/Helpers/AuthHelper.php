<?php

namespace Tests\Helpers;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthHelper
{
    /**
     * Gera um token JWT para um usuário fake e retorna os headers de autenticação.
     */
    public static function getAuthHeaders(): array
    {
        // Criar um usuário fake
        $user = User::factory()->create();

        // Gerar token JWT para o usuário
        $token = JWTAuth::fromUser($user);

        // Retornar os headers de autenticação
        return [
            'Authorization' => 'Bearer ' . $token,
        ];
    }
}