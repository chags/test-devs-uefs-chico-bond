<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Projeto UEFS - Netra",
 *     version="1.0.0",
 *     description="Esta é uma API para gerenciar usuários, posts e tags.",
 *     @OA\Contact(
 *         email="cristiano.chagas@th7.com.br"
 *     ),
 *     @OA\License(
 *         name="MIT License",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="Ambiente de Desenvolvimento"
 * )
 */
class SwaggerDocumentation {}