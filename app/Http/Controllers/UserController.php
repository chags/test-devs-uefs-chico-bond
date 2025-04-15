<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Lista todos os usuários",
     *     description="Retorna uma lista paginada de todos os usuários cadastrados no sistema.",
     *     tags={"Usuários"},
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
     *         description="Lista de usuários retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
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

            // Consulta paginada dos usuários
            $users = User::paginate($perPage, ['*'], 'page', $page);

            // Retorna os usuários no formato JSON
            return response()->json($users);
        } catch (\Exception $e) {
            // Em caso de erro, retorna uma resposta 500
            Log::error('Erro ao listar usuários: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Exibe detalhes de um usuário específico",
     *     description="Retorna os detalhes de um usuário com base no ID fornecido.",
     *     tags={"Usuários"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do usuário retornados com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário não encontrado.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            // Busca o usuário pelo ID no banco de dados
            $user = User::findOrFail($id);

            // Retorna os detalhes do usuário no formato JSON
            return response()->json($user);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Retorna 404 se o usuário não for encontrado
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        } catch (\Exception $e) {
            // Retorna 500 em caso de erro interno
            Log::error('Erro ao exibir usuário: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Cria um novo usuário",
     *     description="Cria um novo usuário no sistema com os dados fornecidos.",
     *     tags={"Usuários"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", example="joao.silva@example.com"),
     *             @OA\Property(property="password", type="string", example="senha123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Os dados fornecidos são inválidos."),
     *             @OA\Property(property="errors", type="object", example={"email": {"O campo email já está em uso."}})
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
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
            ]);

            // Cria o usuário no banco de dados
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']), // Criptografa a senha
            ]);

            Log::info('Usuário criado com sucesso.', ['user_id' => $user->id]);

            DB::commit(); // Confirma a transação

            // Retorna o usuário criado com status 201
            return response()->json($user, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro de validação
            Log::error('Erro de validação ao criar usuário: ' . $e->getMessage(), $e->errors());
            return response()->json(['message' => 'Os dados fornecidos são inválidos.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro interno
            Log::error('Erro interno ao criar usuário: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Atualiza um usuário existente",
     *     description="Atualiza os dados de um usuário existente com base no ID fornecido.",
     *     tags={"Usuários"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Maria Souza"),
     *             @OA\Property(property="email", type="string", example="maria.souza@example.com"),
     *             @OA\Property(property="password", type="string", example="novaSenha123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário não encontrado.")
     *         )
     *     )
     * )
     */
    public function update($id, Request $request)
    {
        DB::beginTransaction(); // Inicia uma transação

        try {
            // Busca o usuário pelo ID no banco de dados
            $user = User::findOrFail($id);

            // Validação dos dados
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'password' => 'sometimes|min:6',
            ]);

            // Atualiza os dados do usuário
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']); // Criptografa a nova senha
            }

            $user->update($validated);

            Log::info('Usuário atualizado com sucesso.', ['user_id' => $user->id]);

            DB::commit(); // Confirma a transação

            // Retorna o usuário atualizado
            return response()->json($user);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro de validação
            Log::error('Erro de validação ao atualizar usuário: ' . $e->getMessage(), $e->errors());
            return response()->json(['message' => 'Os dados fornecidos são inválidos.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro interno
            Log::error('Erro interno ao atualizar usuário: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Remove um usuário",
     *     description="Remove um usuário existente com base no ID fornecido.",
     *     tags={"Usuários"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Usuário removido com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário não encontrado.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            // Busca o usuário pelo ID no banco de dados
            $user = User::findOrFail($id);

            // Remove o usuário
            $user->delete();

            // Retorna status 204 (No Content)
            return response()->noContent();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Retorna 404 se o usuário não for encontrado
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        } catch (\Exception $e) {
            // Retorna 500 em caso de erro interno
            Log::error('Erro ao remover usuário: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }
}