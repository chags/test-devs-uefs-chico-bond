<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Tag",
 *     title="Tag",
 *     description="Representa uma tag no sistema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="PHP"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00Z")
 * )
 */
class TagSchema {}