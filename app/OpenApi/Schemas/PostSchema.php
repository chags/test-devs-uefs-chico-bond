<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Post",
 *     title="Post",
 *     description="Representa um post no sistema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Primeiro Post"),
 *     @OA\Property(property="content", type="string", example="Este é o conteúdo do primeiro post."),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00Z")
 * )
 */
class PostSchema {}