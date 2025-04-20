<?php

namespace Tests\App\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Tag;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class TagControllerTest extends TestCase
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
     * Testa se a listagem de tags retorna uma resposta bem-sucedida.
     */
    public function test_index_returns_tags()
    {
        $this->authenticate();

        // Cria algumas tags no banco de dados
        Tag::factory()->count(3)->create();

        // Faz a requisição GET para o endpoint /tags
        $response = $this->getJson('/api/tags');

        // Verifica se a resposta tem status 200
        $response->assertStatus(200);

        // Verifica se a resposta contém as tags criadas
        $response->assertJsonCount(3, 'data');
    }

    /**
     * Testa se a criação de uma nova tag funciona corretamente.
     */
    public function test_store_creates_a_new_tag()
    {
        $this->authenticate();

        // Dados válidos para criar uma nova tag
        $data = [
            'name' => 'Nova Tag',
        ];

        // Faz a requisição POST para o endpoint /tags
        $response = $this->postJson('/api/tags', $data);

        // Verifica se a resposta tem status 201 (Created)
        $response->assertStatus(201);

        // Verifica se a tag foi criada no banco de dados
        $this->assertDatabaseHas('tags', ['name' => 'Nova Tag']);
    }

    /**
     * Testa a validação ao tentar criar uma tag com dados inválidos.
     */
    public function test_store_validates_required_fields()
    {
        $this->authenticate();

        // Dados inválidos (faltando o campo "name")
        $invalidData = [];

        // Faz a requisição POST para o endpoint /tags
        $response = $this->postJson('/api/tags', $invalidData);

        // Verifica se a resposta tem status 422 (Unprocessable Entity)
        $response->assertStatus(422);

        // Verifica se os erros de validação estão presentes
        $response->assertJsonValidationErrors(['name']);
    }

    /**
     * Testa se a atualização de uma tag funciona corretamente.
     */
    public function test_update_updates_an_existing_tag()
    {
        $this->authenticate();

        // Cria uma tag no banco de dados
        $tag = Tag::factory()->create(['name' => 'Tag Original']);

        // Dados para atualizar a tag
        $updatedData = [
            'name' => 'Tag Atualizada',
        ];

        // Faz a requisição PUT para o endpoint /tags/{id}
        $response = $this->putJson("/api/tags/{$tag->id}", $updatedData);

        // Verifica se a resposta tem status 200
        $response->assertStatus(200);

        // Verifica se a tag foi atualizada no banco de dados
        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'Tag Atualizada']);
    }

    /**
     * Testa se a exclusão de uma tag funciona corretamente.
     */
    public function test_destroy_deletes_a_tag()
    {
        $this->authenticate();

        // Cria uma tag no banco de dados
        $tag = Tag::factory()->create();

        // Faz a requisição DELETE para o endpoint /tags/{id}
        $response = $this->deleteJson("/api/tags/{$tag->id}");

        // Verifica se a resposta tem status 204 (No Content)
        $response->assertStatus(204);

        // Verifica se a tag foi removida do banco de dados
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}