<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Cria ou busca o usuário pelo email
        $user = User::firstOrCreate(
            ['email' => 'chico.bond@examplechags.com'], // Condição de busca
            [
                'name' => 'Cris Bond',
                'password' => bcrypt('password') // Senha criptografada
            ]
        );

        // Gera o token JWT para o usuário
        try {
            $token = JWTAuth::fromUser($user);

            // Exibe o token no terminal
            echo "Token JWT gerado: " . $token . "\n";
        } catch (\Exception $e) {
            // Em caso de erro, exibe uma mensagem
            echo "Erro ao gerar o token JWT: " . $e->getMessage() . "\n";
        }
    }
}