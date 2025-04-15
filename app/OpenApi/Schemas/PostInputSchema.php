<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="PostInput",
 *     title="PostInput",
 *     description="Dados necessários para criar ou atualizar um post",
 *     @OA\Property(property="title", type="string", example="Primeiro Post"),
 *     @OA\Property(property="content", type="string", example="Este é o conteúdo do primeiro post."),
 *     @OA\Property(property="user_id", type="integer", example=1)
 * )
 */
class PostInputSchema {}