<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * Bootstrap minimal path so swagger-php always has at least one PathItem.
 *
 * @OA\Get(
 *   path="/api",
 *   tags={"System"},
 *   summary="API health (welcome)",
 *   @OA\Response(response=200, description="OK")
 * )
 */
class Paths
{
}
