<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // Importação necessária para Auth::id()
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class PostService
{
    /**
     * Lista os posts com paginação.
     *
     * @param int $page
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listPosts(int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        return Post::paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Busca um post pelo ID.
     * Lança ModelNotFoundException se não encontrado.
     *
     * @param int $id
     * @return Post
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getPostById(int $id): Post
    {
        return Post::findOrFail($id);
    }

    /**
     * Cria um novo post com suas tags.
     *
     * @param array $data Dados validados do post (title, content, ?tags)
     * @return Post
     * @throws \Exception|\Throwable Em caso de erro na criação ou transação.
     */
    public function createPost(array $data): Post
    {
        DB::beginTransaction();
        try {
            // Remove o user_id enviado na requisição (se houver)
            unset($data['user_id']);

            // Adiciona automaticamente o ID do usuário autenticado
            $data['user_id'] = Auth::id();

            // Gera o slug automaticamente
            $data['slug'] = Str::slug($data['title']);

            // Cria o post
            $post = Post::create($data);
            Log::info('Post criado com sucesso (antes das tags).', ['post_id' => $post->id]);

            // Processa e associa as tags, se fornecidas
            if (isset($data['tags'])) {
                $tagIds = $this->processTags($data['tags']);
                $post->tags()->attach($tagIds);
                Log::info('Tags associadas ao post criado.', ['post_id' => $post->id, 'tag_ids' => $tagIds]);
            }

            DB::commit();
            // Carrega as tags para retornar o objeto completo
            return $post->load('tags');

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao criar post no serviço: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Atualiza um post existente e suas tags.
     *
     * @param int $id ID do post a ser atualizado
     * @param array $data Dados validados para atualização
     * @return Post
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception|\Throwable Em caso de erro na atualização ou transação.
     */
    public function updatePost(int $id, array $data): Post
    {
        DB::beginTransaction();
        try {
            $post = $this->getPostById($id);
    
            // Verifica se o usuário autenticado é o proprietário do post
            if ($post->user_id !== Auth::id()) {
                throw new \Exception("Você não tem permissão para atualizar este post.");
            }
    
            // Remove o user_id enviado na requisição (se houver)
            unset($data['user_id']);
    
            // Atualiza o slug se o título foi alterado
            if (isset($data['title']) && $data['title'] !== $post->title) {
                $data['slug'] = Str::slug($data['title']);
                Log::info('Slug do post será atualizado.', ['post_id' => $id, 'new_slug' => $data['slug']]);
            }
    
            // Atualiza apenas os campos enviados na requisição
            $post->update($data);
            Log::info('Dados do post atualizados.', ['post_id' => $id]);
    
            // Processa e sincroniza as tags, se fornecidas
            if (array_key_exists('tags', $data)) {
                $tagIds = $this->processTags($data['tags'] ?? []);
                $post->tags()->sync($tagIds);
                Log::info('Tags do post sincronizadas.', ['post_id' => $post->id, 'tag_ids' => $tagIds]);
            }
    
            DB::commit();
            return $post->load('tags');
    
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar post no serviço: ' . $e->getMessage(), [
                'post_id' => $id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Deleta um post.
     *
     * @param int $id
     * @return bool Retorna true se deletado com sucesso.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception|\Throwable Em caso de erro na deleção.
     */
    public function deletePost(int $id): bool
    {
        DB::beginTransaction();
        try {
            // Busca o post pelo ID
            $post = $this->getPostById($id);
    
            // Verifica se o usuário autenticado é o proprietário do post
            if ($post->user_id !== Auth::id()) {
                throw new \Exception("Você não tem permissão para excluir este post.");
            }
    
            // Remove o post
            $post->delete();
    
            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao excluir post no serviço: ' . $e->getMessage(), [
                'post_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Processa uma lista de nomes de tags, criando as que não existem,
     * e retorna um array com os IDs das tags.
     *
     * @param array $tagNames Array de nomes de tags.
     * @return array Array de IDs das tags.
     */
    protected function processTags(array $tagNames): array
    {
        if (empty($tagNames)) {
            return [];
        }

        return collect($tagNames)->map(function ($tagName) {
            if (empty(trim($tagName))) return null; // Ignora tags vazias

            $tagSlug = Str::slug($tagName);
            $tag = Tag::firstOrCreate(
                ['slug' => $tagSlug],
                ['name' => trim($tagName)]
            );
            return $tag->id;
        })->filter()->unique()->toArray(); // filter para remover nulls, unique e toArray
    }
}