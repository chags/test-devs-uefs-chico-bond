<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable; // Use Throwable para pegar Exceptions e Errors

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
        // Adicione eager loading se as tags forem frequentemente necessárias na listagem
        // return Post::with('tags')->paginate($perPage, ['*'], 'page', $page);
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
        // Adicione eager loading das tags se necessário
        // return Post::with('tags')->findOrFail($id);
        return Post::findOrFail($id);
    }

    /**
     * Cria um novo post com suas tags.
     *
     * @param array $data Dados validados do post (title, content, user_id, ?tags)
     * @return Post
     * @throws \Exception|\Throwable Em caso de erro na criação ou transação.
     */
    public function createPost(array $data): Post
    {
        DB::beginTransaction();
        try {
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

        } catch (Throwable $e) { // Captura Throwable para pegar mais tipos de erros
            DB::rollBack();
            Log::error('Erro ao criar post no serviço: ' . $e->getMessage(), [
                'data' => $data, // Cuidado ao logar dados sensíveis
                'trace' => $e->getTraceAsString(),
            ]);
            // Re-lança a exceção para ser tratada pelo controller ou handler global
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
            $post = $this->getPostById($id); // Reutiliza o método para buscar e já trata o 404 se não achar

            // Atualiza o slug se o título foi alterado
            if (isset($data['title']) && $data['title'] !== $post->title) {
                $data['slug'] = Str::slug($data['title']);
                Log::info('Slug do post será atualizado.', ['post_id' => $id, 'new_slug' => $data['slug']]);
            }

            // Atualiza os dados do post
            $post->update($data);
            Log::info('Dados do post atualizados.', ['post_id' => $id]);


            // Processa e sincroniza as tags, se fornecidas
            // Se 'tags' não estiver presente no $data, mantém as tags existentes.
            // Se 'tags' for um array vazio [], remove todas as tags.
            if (array_key_exists('tags', $data)) { // Verifica se a chave existe, mesmo que seja null ou []
                 $tagIds = $this->processTags($data['tags'] ?? []); // Usa array vazio se tags for null
                 $post->tags()->sync($tagIds); // sync lida com adicionar/remover
                 Log::info('Tags do post sincronizadas.', ['post_id' => $post->id, 'tag_ids' => $tagIds]);
            }


            DB::commit();
            // Carrega as tags para retornar o objeto completo
            return $post->load('tags');

        } catch (Throwable $e) {
            DB::rollBack();
             Log::error('Erro ao atualizar post no serviço: ' . $e->getMessage(), [
                'post_id' => $id,
                'data' => $data, // Cuidado ao logar dados sensíveis
                'trace' => $e->getTraceAsString(),
            ]);
            // Re-lança a exceção para ser tratada pelo controller ou handler global
            // ModelNotFoundException já será lançada por getPostById se necessário
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
        try {
            $post = $this->getPostById($id); // Reutiliza o método para buscar
            // A relação com tags (pivot table) deve ser configurada com onDelete('cascade')
            // ou você pode desanexar manualmente antes: $post->tags()->detach();
            $deleted = $post->delete();
             if ($deleted) {
                Log::info('Post deletado com sucesso.', ['post_id' => $id]);
            } else {
                 Log::warning('Falha ao deletar post (método delete retornou false).', ['post_id' => $id]);
            }
            return $deleted;

        } catch (Throwable $e) {
             Log::error('Erro ao deletar post no serviço: ' . $e->getMessage(), [
                'post_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);
             // Re-lança a exceção
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
            // firstOrCreate para evitar race conditions (embora não 100% garantido sem lock)
            $tag = Tag::firstOrCreate(
                ['slug' => $tagSlug], // Busca pelo slug para garantir unicidade baseada nele
                ['name' => trim($tagName)]  // Cria com nome e slug se não encontrar pelo slug
            );
             // Se encontrou pelo slug mas o nome é diferente (caso raro, mas possível), atualiza o nome?
             // Decision: Manter o nome original encontrado pelo slug. Ou pode adicionar lógica para atualizar.
             // if ($tag->name !== trim($tagName)) {
             //     Log::info('Tag encontrada pelo slug, mas nome difere.', ['slug' => $tagSlug, 'found_name' => $tag->name, 'requested_name' => trim($tagName)]);
             //     // Opcional: $tag->update(['name' => trim($tagName)]);
             // }
            return $tag->id;
        })->filter()->unique()->toArray(); // filter para remover nulls, unique e toArray
    }
}