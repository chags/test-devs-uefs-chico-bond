<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Post",
 *     title="Post Model",
 *     description="Representa um post no blog",
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Título do Post"),
 *     @OA\Property(property="slug", type="string", readOnly=true, example="titulo-do-post"),
 *     @OA\Property(property="content", type="string", example="Conteúdo do post..."),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true),
 *     @OA\Property(property="tags", type="array", @OA\Items(ref="#/components/schemas/Tag"))
 * )
 */
class PostSchema {}