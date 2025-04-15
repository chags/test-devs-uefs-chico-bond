<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UserInput",
 *     title="UserInput",
 *     description="Dados necessários para criar ou atualizar um usuário",
 *     @OA\Property(property="name", type="string", example="Maria Souza"),
 *     @OA\Property(property="email", type="string", example="maria.souza@example.com"),
 *     @OA\Property(property="password", type="string", example="senha123")
 * )
 */
class UserInputSchema {}