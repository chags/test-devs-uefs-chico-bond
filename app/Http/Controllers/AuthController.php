<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth; // Importe o facade JWTAuth
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Autenticar usuário e gerar token JWT",
     *     description="Recebe as credenciais do usuário e retorna um token JWT.",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="professor@uefs.gov.br"),
     *             @OA\Property(property="password", type="string", example="admin123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token JWT gerado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Credenciais inválidas.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro interno do servidor.")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();

            // Log das credenciais recebidas (sem incluir a senha)
            Log::info('Tentativa de login com credenciais:', ['email' => $credentials['email']]);

            // Tente autenticar o usuário e gerar o token JWT
            if (!$token = JWTAuth::attempt($credentials)) {
                Log::warning('Falha na autenticação com credenciais:', ['email' => $credentials['email']]);
                return response()->json(['message' => 'Credenciais inválidas.'], 401);
            }

            return response()->json(['token' => $token]);
        } catch (JWTException $e) {
            Log::error('Erro ao gerar token JWT: ' . $e->getMessage());
            return response()->json(['message' => 'Não foi possível gerar o token.'], 500);
        }
    }
}