<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class PostController extends Controller
{

    /**
     * @OA\Get(
     *     path="/posts",
     *     summary="Lista todos os posts",
     *     description="Retorna uma lista paginada de todos os posts cadastrados no sistema.",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página para paginação",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Quantidade de itens por página",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de posts retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Post")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            // Parâmetros de paginação
            $page = $request->input('page', 1); // Página padrão é 1
            $perPage = $request->input('per_page', 10); // Itens por página padrão é 10

            // Consulta paginada dos posts
            $posts = Post::paginate($perPage, ['*'], 'page', $page);

            // Retorna os posts no formato JSON
            return response()->json($posts);
        } catch (\Exception $e) {
            // Em caso de erro, retorna uma resposta 500
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/posts/{id}",
     *     summary="Exibe detalhes de um post específico",
     *     description="Retorna os detalhes de um post com base no ID fornecido.",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do post",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do post retornados com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post não encontrado.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            // Busca o post pelo ID no banco de dados
            $post = Post::findOrFail($id);

            // Retorna os detalhes do post no formato JSON
            return response()->json($post);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Retorna 404 se o post não for encontrado
            return response()->json(['message' => 'Post não encontrado.'], 404);
        } catch (\Exception $e) {
            // Retorna 500 em caso de erro interno
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/posts",
     *     summary="Cria um novo post",
     *     description="Cria um novo post no sistema com os dados fornecidos.",
     *     tags={"Posts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content", "user_id", "tags"},
     *             @OA\Property(property="title", type="string", example="Título do Post"),
     *             @OA\Property(property="content", type="string", example="Conteúdo do post..."),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"Laravel", "API"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Os dados fornecidos são inválidos."),
     *             @OA\Property(property="errors", type="object", example={"tags": {"A tag 'Laravel' já existe."}})
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction(); // Inicia uma transação

        try {
            // Validação dos dados
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
                'tags' => 'required|array',
                'tags.*' => 'required|string|max:255',
            ]);

            Log::info('Validação concluída com sucesso.', ['dados_validados' => $validated]);

            // Gera o slug automaticamente a partir do título do post
            $slug = Str::slug($validated['title']);
            Log::info('Slug do post gerado com sucesso.', ['slug' => $slug]);

            // Adiciona o slug aos dados validados
            $validated['slug'] = $slug;

            // Cria o post no banco de dados
            $post = Post::create($validated);
            Log::info('Post criado com sucesso.', ['post_id' => $post->id]);

            // Processa as tags
            $tags = collect($validated['tags'])->map(function ($tagName) use ($post) {
                Log::info('Processando tag.', ['tag_name' => $tagName]);

                // Gera o slug da tag automaticamente
                $tagSlug = Str::slug($tagName);

                // Verifica se a tag já existe; se não, cria-a
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName], // Condição de busca
                    ['slug' => $tagSlug]  // Dados adicionais para criação
                );

                Log::info('Tag processada com sucesso.', ['tag_id' => $tag->id]);
                return $tag->id;
            });

            // Associa as tags ao post
            $post->tags()->attach($tags);
            Log::info('Tags associadas ao post com sucesso.', ['post_id' => $post->id, 'tag_ids' => $tags]);

            DB::commit(); // Confirma a transação

            // Retorna o post criado com status 201
            return response()->json($post->load('tags'), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro de validação
            Log::error('Erro de validação ao criar post: ' . $e->getMessage(), $e->errors());
            return response()->json(['message' => 'Os dados fornecidos são inválidos.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro interno
            Log::error('Erro interno ao criar post: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/posts/{id}",
     *     summary="Atualiza um post existente",
     *     description="Atualiza os dados de um post existente com base no ID fornecido.",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do post",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Novo Título"),
     *             @OA\Property(property="content", type="string", example="Novo conteúdo do post..."),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"Laravel", "Nova Tag"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post não encontrado.")
     *         )
     *     )
     * )
     */
    public function update($id, Request $request)
    {
        DB::beginTransaction(); // Inicia uma transação

        try {
            // Busca o post pelo ID no banco de dados
            $post = Post::findOrFail($id);

            // Validação dos dados
            $validated = $request->validate([
                'title'   => 'required|string|max:255',
                'content' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
                'tags'    => 'sometimes|array',
                'tags.*'  => 'sometimes|string|max:255',
            ]);
            

            Log::info('Validação concluída com sucesso.', ['dados_validados' => $validated]);

            // Atualiza o slug se o título foi alterado
            if (isset($validated['title'])) {
                $slug = Str::slug($validated['title']);
                $validated['slug'] = $slug;
                Log::info('Slug do post atualizado com sucesso.', ['slug' => $slug]);
            }

            // Atualiza os dados do post
            $post->update($validated);

            // Processa as tags, se fornecidas
            if (isset($validated['tags'])) {
                $tags = collect($validated['tags'])->map(function ($tagName) use ($post) {
                    Log::info('Processando tag.', ['tag_name' => $tagName]);

                    // Gera o slug da tag automaticamente
                    $tagSlug = Str::slug($tagName);

                    // Verifica se a tag já existe; se não, cria-a
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagName], // Condição de busca
                        ['slug' => $tagSlug]  // Dados adicionais para criação
                    );

                    Log::info('Tag processada com sucesso.', ['tag_id' => $tag->id]);
                    return $tag->id;
                });

                // Sincroniza as tags associadas ao post
                $post->tags()->sync($tags);
                Log::info('Tags sincronizadas com sucesso.', ['post_id' => $post->id, 'tag_ids' => $tags]);
            }

            DB::commit(); // Confirma a transação

            // Retorna o post atualizado
            return response()->json($post->load('tags'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro
            Log::error('Post não encontrado: ' . $e->getMessage());
            return response()->json(['message' => 'Post não encontrado.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro de validação
            Log::error('Erro de validação ao atualizar post: ' . $e->getMessage(), $e->errors());
            return response()->json(['message' => 'Os dados fornecidos são inválidos.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro interno
            Log::error('Erro interno ao atualizar post: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/posts/{id}",
     *     summary="Remove um post",
     *     description="Remove um post existente com base no ID fornecido.",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do post",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Post removido com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post não encontrado.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            // Busca o post pelo ID no banco de dados
            $post = Post::findOrFail($id);

            // Remove o post
            $post->delete();

            // Retorna status 204 (No Content)
            return response()->noContent();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Retorna 404 se o post não for encontrado
            return response()->json(['message' => 'Post não encontrado.'], 404);
        } catch (\Exception $e) {
            // Retorna 500 em caso de erro interno
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }
}