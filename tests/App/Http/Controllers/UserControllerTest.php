<?php

namespace Tests\App\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserControllerTest extends TestCase
{
    use RefreshDatabase; // Executa as migrações antes de cada teste

    /**
     * Autentica um usuário e configura o token JWT.
     */
    protected function authenticate()
    {
        // Cria um usuário falso e autentica-o
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', "Bearer {$token}");
    }

    /**
     * Testa se a listagem de usuários retorna uma resposta bem-sucedida.
     */
    public function test_index_returns_users()
    {
        // Autentica o usuário
        $this->authenticate();
    
        // Cria alguns usuários no banco de dados
        User::factory()->count(3)->create();
    
        // Faz a requisição GET para o endpoint /users
        $response = $this->getJson('/api/users');
    
        // Verifica se a resposta tem status 200
        $response->assertStatus(200);
    
        // Verifica se a resposta contém os usuários criados (excluindo o usuário autenticado)
        $response->assertJsonCount(4, 'data'); // Total de 4 usuários: 3 criados + 1 autenticado
    }
    /**
     * Testa se a criação de um novo usuário funciona corretamente.
     */
    public function test_store_creates_a_new_user()
    {
        $this->authenticate();

        // Dados válidos para criar um novo usuário
        $data = [
            'name' => 'Novo Usuário',
            'email' => 'novo.usuario@example.com',
            'password' => 'senha123',
        ];

        // Faz a requisição POST para o endpoint /users
        $response = $this->postJson('/api/users', $data);

        // Verifica se a resposta tem status 201 (Created)
        $response->assertStatus(201);

        // Verifica se o usuário foi criado no banco de dados
        $this->assertDatabaseHas('users', ['email' => 'novo.usuario@example.com']);
    }

    /**
     * Testa a validação ao tentar criar um usuário com dados inválidos.
     */
    public function test_store_validates_required_fields()
    {
        $this->authenticate();

        // Dados inválidos (faltando campos obrigatórios)
        $invalidData = [];

        // Faz a requisição POST para o endpoint /users
        $response = $this->postJson('/api/users', $invalidData);

        // Verifica se a resposta tem status 422 (Unprocessable Entity)
        $response->assertStatus(422);

        // Verifica se os erros de validação estão presentes
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Testa se a atualização de um usuário funciona corretamente.
     */
    public function test_update_updates_an_existing_user()
    {
        $this->authenticate();

        // Cria um usuário no banco de dados
        $user = User::factory()->create([
            'name' => 'Nome Original',
            'email' => 'original@example.com',
        ]);

        // Dados para atualizar o usuário
        $updatedData = [
            'name' => 'Nome Atualizado',
            'email' => 'atualizado@example.com',
        ];

        // Faz a requisição PUT para o endpoint /users/{id}
        $response = $this->putJson("/api/users/{$user->id}", $updatedData);

        // Verifica se a resposta tem status 200
        $response->assertStatus(200);

        // Verifica se o usuário foi atualizado no banco de dados
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nome Atualizado',
            'email' => 'atualizado@example.com',
        ]);
    }

    /**
     * Testa se a exclusão de um usuário funciona corretamente.
     */
    public function test_destroy_deletes_a_user()
    {
        $this->authenticate();

        // Cria um usuário no banco de dados
        $user = User::factory()->create();

        // Faz a requisição DELETE para o endpoint /users/{id}
        $response = $this->deleteJson("/api/users/{$user->id}");

        // Verifica se a resposta tem status 204 (No Content)
        $response->assertStatus(204);

        // Verifica se o usuário foi removido do banco de dados
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}