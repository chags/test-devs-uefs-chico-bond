<?php

namespace Tests\Traits;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait Authenticates
{
    /**
     * Configura a autenticação JWT para as requisições.
     */
    protected function authenticate()
    {
        // Criar um usuário fake
        $user = User::factory()->create();

        // Gerar token JWT para o usuário
        $token = JWTAuth::fromUser($user);

        // Configurar o cabeçalho Authorization globalmente
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);
    }
}