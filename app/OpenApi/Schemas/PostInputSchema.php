<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *      schema="StorePostRequest",
 *      title="Store Post Request Body",
 *      required={"title", "content", "user_id"},
 *      @OA\Property(property="title", type="string", maxLength=255, example="Meu Primeiro Post"),
 *      @OA\Property(property="content", type="string", example="Este é o conteúdo do meu primeiro post."),
 *      @OA\Property(property="user_id", type="integer", description="ID do usuário autor do post", example=1),
 *      @OA\Property(property="tags", type="array", @OA\Items(type="string"), description="Lista de nomes de tags", example={"PHP", "Laravel", "API"})
 * )
 *
 * @OA\Schema(
 *      schema="UpdatePostRequest",
 *      title="Update Post Request Body",
 *      required={"title", "content", "user_id"},
 *      @OA\Property(property="title", type="string", maxLength=255, example="Meu Post Atualizado"),
 *      @OA\Property(property="content", type="string", example="Conteúdo atualizado do post."),
 *      @OA\Property(property="user_id", type="integer", description="ID do usuário autor do post", example=1),
 *      @OA\Property(property="tags", type="array", @OA\Items(type="string"), description="Lista de nomes de tags (substitui as existentes)", example={"Laravel 11", "SOLID"})
 * )
 */
class PostInputSchema {}