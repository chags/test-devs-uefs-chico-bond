<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="TagInput",
 *     title="TagInput",
 *     description="Dados necessários para criar ou atualizar uma tag",
 *     @OA\Property(property="name", type="string", example="PHP")
 * )
 */
class TagInputSchema {}