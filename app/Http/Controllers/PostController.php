<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * @OA\Get(
     *     path="/posts",
     *     summary="Lista todos os posts",
     *     description="Retorna uma lista paginada de todos os posts cadastrados no sistema.",
     *     tags={"Posts"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página para paginação",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Quantidade de itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de posts retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Post")),
     *             @OA\Property(property="first_page_url", type="string"),
     *             @OA\Property(property="from", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="last_page_url", type="string"),
     *             @OA\Property(property="path", type="string"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="to", type="integer"),
     *             @OA\Property(property="total", type="integer"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Erro interno do servidor."))
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10);
            $posts = $this->postService->listPosts($page, $perPage);
            return response()->json($posts);
        } catch (\Throwable $e) {
            Log::error('Erro inesperado no PostController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor.'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/posts/{id}",
     *     summary="Exibe detalhes de um post específico",
     *     description="Retorna os detalhes de um post com base no ID fornecido.",
     *     tags={"Posts"},
     *     security={{"BearerAuth": {}}},
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
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Post não encontrado."))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Erro interno do servidor."))
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $post = $this->postService->getPostById($id);
            return response()->json($post);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Post não encontrado.'], 404);
        } catch (\Throwable $e) {
            Log::error('Erro inesperado no PostController@show: ' . $e->getMessage(), ['post_id' => $id]);
            return response()->json(['message' => 'Erro interno do servidor.'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/posts",
     *     summary="Cria um novo post",
     *     description="Cria um novo post no sistema com os dados fornecidos.",
     *     tags={"Posts"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content"},
     *             @OA\Property(property="title", type="string", example="Meu Primeiro Post"),
     *             @OA\Property(property="content", type="string", example="Este é o conteúdo do meu primeiro post."),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"PHP", "Laravel", "API"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"title": {"O campo título é obrigatório."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Erro interno do servidor."))
     *     )
     * )
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            // Os dados validados não incluem user_id, pois ele será preenchido automaticamente no serviço
            $validatedData = $request->validated();
            $post = $this->postService->createPost($validatedData);
            return response()->json($post, 201);
        } catch (\Throwable $e) {
            Log::error('Erro inesperado no PostController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno ao criar post.'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/posts/{id}",
     *     summary="Atualiza um post existente",
     *     description="Atualiza os dados de um post existente com base no ID fornecido.",
     *     tags={"Posts"},
     *     security={{"BearerAuth": {}}},
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
     *             @OA\Property(property="title", type="string", example="Título Atualizado"),
     *             @OA\Property(property="content", type="string", example="Conteúdo atualizado do post."),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"Laravel 11", "SOLID"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Post não encontrado."))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"title": {"O campo título é obrigatório."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Erro interno do servidor."))
     *     )
     * )
     */
    public function update(UpdatePostRequest $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            Log::info('Dados validados para atualização:', $validatedData); // Log para depuração
            $post = $this->postService->updatePost($id, $validatedData);
            return response()->json($post);
        } catch (ModelNotFoundException $e) {
            Log::error('Post não encontrado durante a atualização.', ['post_id' => $id]);
            return response()->json(['message' => 'Post não encontrado.'], 404);
        } catch (ValidationException $e) {
            Log::error('Erro de validação durante a atualização.', ['errors' => $e->errors()]);
            return response()->json(['message' => 'Os dados fornecidos são inválidos.', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Erro inesperado no PostController@update: ' . $e->getMessage(), ['post_id' => $id]);
            return response()->json(['message' => 'Erro interno ao atualizar post.'], 500);
        }
    }
    /**
     * @OA\Delete(
     *     path="/posts/{id}",
     *     summary="Remove um post",
     *     description="Remove um post existente com base no ID fornecido.",
     *     tags={"Posts"},
     *     security={{"BearerAuth": {}}},
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
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Post não encontrado."))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Erro interno do servidor."))
     *     )
     * )
     */
    public function destroy(int $id): Response|JsonResponse
    {
        try {
            $deleted = $this->postService->deletePost($id);
            if ($deleted) {
                return response()->noContent();
            } else {
                Log::error('Falha ao deletar post no PostController@destroy, serviço retornou false.', ['post_id' => $id]);
                return response()->json(['message' => 'Não foi possível remover o post.'], 500);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Post não encontrado.'], 404);
        } catch (\Throwable $e) {
            Log::error('Erro inesperado no PostController@destroy: ' . $e->getMessage(), ['post_id' => $id]);
            return response()->json(['message' => 'Erro interno ao remover o post.'], 500);
        }
    }
}