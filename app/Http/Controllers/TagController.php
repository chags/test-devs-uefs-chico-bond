<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Tag;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class TagController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tags",
     *     summary="Lista todas as tags",
     *     description="Retorna uma lista paginada de todas as tags cadastradas no sistema.",
     *     tags={"Tags"},
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
     *         description="Lista de tags retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Tag")
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

            // Consulta paginada das tags
            $tags = Tag::paginate($perPage, ['*'], 'page', $page);

            // Retorna as tags no formato JSON
            return response()->json($tags);
        } catch (\Exception $e) {
            // Em caso de erro, retorna uma resposta 500
            Log::error('Erro ao listar tags: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tags/{id}",
     *     summary="Exibe detalhes de uma tag específica",
     *     description="Retorna os detalhes de uma tag com base no ID fornecido.",
     *     tags={"Tags"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da tag",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da tag retornados com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tag não encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tag não encontrada.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            // Busca a tag pelo ID no banco de dados
            $tag = Tag::findOrFail($id);

            // Retorna os detalhes da tag no formato JSON
            return response()->json($tag);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Retorna 404 se a tag não for encontrada
            return response()->json(['message' => 'Tag não encontrada.'], 404);
        } catch (\Exception $e) {
            // Retorna 500 em caso de erro interno
            Log::error('Erro ao exibir tag: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tags",
     *     summary="Cria uma nova tag",
     *     description="Cria uma nova tag no sistema com os dados fornecidos.",
     *     tags={"Tags"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="PHP")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tag criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Os dados fornecidos são inválidos."),
     *             @OA\Property(property="errors", type="object", example={"name": {"O campo nome é obrigatório."}})
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
                'name' => 'required|string|max:255|unique:tags,name',
            ]);

            // Gera o slug automaticamente
            $slug = Str::slug($validated['name']);

            // Cria a tag no banco de dados
            $tag = Tag::create([
                'name' => $validated['name'],
                'slug' => $slug,
            ]);

            Log::info('Tag criada com sucesso.', ['tag_id' => $tag->id]);

            DB::commit(); // Confirma a transação

            // Retorna a tag criada com status 201
            return response()->json($tag, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro de validação
            Log::error('Erro de validação ao criar tag: ' . $e->getMessage(), $e->errors());
            return response()->json(['message' => 'Os dados fornecidos são inválidos.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro interno
            Log::error('Erro interno ao criar tag: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/tags/{id}",
     *     summary="Atualiza uma tag existente",
     *     description="Atualiza os dados de uma tag existente com base no ID fornecido.",
     *     tags={"Tags"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da tag",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Laravel Framework")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tag não encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tag não encontrada.")
     *         )
     *     )
     * )
     */
    public function update($id, Request $request)
    {
        DB::beginTransaction(); // Inicia uma transação

        try {
            // Busca a tag pelo ID no banco de dados
            $tag = Tag::findOrFail($id);

            // Validação dos dados
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:tags,name,' . $id,
            ]);

            // Gera o slug automaticamente
            $slug = Str::slug($validated['name']);

            // Atualiza os dados da tag
            $tag->update([
                'name' => $validated['name'],
                'slug' => $slug,
            ]);

            Log::info('Tag atualizada com sucesso.', ['tag_id' => $tag->id]);

            DB::commit(); // Confirma a transação

            // Retorna a tag atualizada
            return response()->json($tag);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro
            return response()->json(['message' => 'Tag não encontrada.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro de validação
            Log::error('Erro de validação ao atualizar tag: ' . $e->getMessage(), $e->errors());
            return response()->json(['message' => 'Os dados fornecidos são inválidos.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro interno
            Log::error('Erro interno ao atualizar tag: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/tags/{id}",
     *     summary="Remove uma tag",
     *     description="Remove uma tag existente com base no ID fornecido.",
     *     tags={"Tags"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da tag",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Tag removida com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tag não encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tag não encontrada.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            // Busca a tag pelo ID no banco de dados
            $tag = Tag::findOrFail($id);

            // Remove a tag
            $tag->delete();

            // Retorna status 204 (No Content)
            return response()->noContent();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Retorna 404 se a tag não for encontrada
            return response()->json(['message' => 'Tag não encontrada.'], 404);
        } catch (\Exception $e) {
            // Retorna 500 em caso de erro interno
            Log::error('Erro ao remover tag: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }
}