<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="Representa um usuário no sistema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="João Silva"),
 *     @OA\Property(property="email", type="string", example="joao.silva@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00Z")
 * )
 */
class UserSchema {}