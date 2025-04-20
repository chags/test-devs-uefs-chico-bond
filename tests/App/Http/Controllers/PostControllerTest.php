<?php

namespace Tests\App\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostControllerTest extends TestCase
{
    use RefreshDatabase; // Limpa o banco de dados após cada teste

    /**
     * Autentica um usuário e configura o token JWT.
     */
    protected function authenticate()
    {
        // Cria um usuário falso e autentica-o
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', "Bearer {$token}");
        Auth::setUser($user); // Define o usuário autenticado no contexto do teste
    }

    /**
     * Testa a listagem de posts.
     */
    public function test_index_returns_paginated_posts()
    {
        $this->authenticate();

        // Cria alguns posts no banco de dados
        Post::factory()->count(3)->create();

        // Faz a requisição GET para o endpoint /posts
        $response = $this->getJson('/api/posts');

        // Verifica se a resposta tem status 200
        $response->assertStatus(200);

        // Verifica se os posts estão presentes na resposta
        $response->assertJsonCount(3, 'data');
    }

    /**
     * Testa a exibição de um post específico.
     */
    public function test_show_returns_post_details()
    {
        $this->authenticate();

        // Cria um post no banco de dados
        $post = Post::factory()->create();

        // Faz a requisição GET para o endpoint /posts/{id}
        $response = $this->getJson("/api/posts/{$post->id}");

        // Verifica se a resposta tem status 200
        $response->assertStatus(200);

        // Verifica se os detalhes do post estão corretos
        $response->assertJson([
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
        ]);
    }

    /**
     * Testa a criação de um novo post.
     */
    public function test_store_creates_a_new_post()
    {
        $this->authenticate();

        // Dados para criar um novo post
        $postData = [
            'title' => 'Novo Post',
            'content' => 'Conteúdo do novo post...',
            'tags' => ['Laravel', 'API'],
        ];

        // Faz a requisição POST para o endpoint /posts
        $response = $this->postJson('/api/posts', $postData);

        // Verifica se a resposta tem status 201
        $response->assertStatus(201);

        // Verifica se o post foi criado no banco de dados
        $this->assertDatabaseHas('posts', [
            'title' => 'Novo Post',
            'content' => 'Conteúdo do novo post...',
        ]);

        // Verifica se as tags foram associadas ao post
        $response->assertJsonFragment(['name' => 'Laravel']);
        $response->assertJsonFragment(['name' => 'API']);
    }

    /**
     * Testa a atualização de um post existente.
     */
    public function test_update_updates_an_existing_post()
    {
        $this->authenticate();
    
        // Cria um post no banco de dados associado ao usuário autenticado
        $post = Post::factory()->create(['user_id' => Auth::id()]);
    
        // Dados para atualizar o post
        $updatedData = [
            'title' => 'Título Atualizado',
            'content' => 'Conteúdo atualizado...',
            'tags' => ['Nova Tag'],
        ];
    
        // Faz a requisição PUT para o endpoint /posts/{id}
        $response = $this->putJson("/api/posts/{$post->id}", $updatedData);
    
        // Verifica se a resposta tem status 200
        $response->assertStatus(200);
    
        // Verifica se o post foi atualizado no banco de dados
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Título Atualizado',
            'content' => 'Conteúdo atualizado...',
        ]);
    
        // Verifica se as tags foram atualizadas
        $response->assertJsonFragment(['name' => 'Nova Tag']);
    }

    /**
     * Testa a exclusão de um post.
     */
    public function test_destroy_deletes_a_post()
    {
        $this->authenticate();
    
        // Cria um post no banco de dados associado ao usuário autenticado
        $post = Post::factory()->create(['user_id' => Auth::id()]);
    
        // Faz a requisição DELETE para o endpoint /posts/{id}
        $response = $this->deleteJson("/api/posts/{$post->id}");
    
        // Verifica se a resposta tem status 204
        $response->assertStatus(204);
    
        // Verifica se o post foi removido do banco de dados
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /**
     * Testa a validação de campos obrigatórios ao criar um post.
     */
    public function test_store_validates_required_fields()
    {
        $this->authenticate();

        // Dados inválidos (faltando campos obrigatórios)
        $invalidData = [];

        // Faz a requisição POST para o endpoint /posts
        $response = $this->postJson('/api/posts', $invalidData);

        // Verifica se a resposta tem status 422
        $response->assertStatus(422);

        // Verifica se os erros de validação estão presentes
        $response->assertJsonValidationErrors(['title', 'content']);
    }

    /**
     * Testa a validação de campos obrigatórios ao atualizar um post.
     */
    public function test_update_validates_required_fields()
    {
        $this->authenticate();
    
        // Cria um post no banco de dados
        $post = Post::factory()->create();
    
        // Dados inválidos (faltando campos obrigatórios)
        $invalidData = [];
    
        // Faz a requisição PUT para o endpoint /posts/{id}
        $response = $this->putJson("/api/posts/{$post->id}", $invalidData);
    
        // Verifica se a resposta tem status 422
        $response->assertStatus(422);
    
        // Verifica se os erros de validação estão presentes
        $response->assertJsonValidationErrors(['title', 'content']);
    }
}