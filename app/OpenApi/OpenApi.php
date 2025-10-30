<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *   @OA\Info(
 *     title="Q Medis API",
 *     version="1.0.0",
 *     description="Q Medis API documentation"
 *   ),
 *   @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local"
 *   ),
 *   @OA\Components(
 *     @OA\SecurityScheme(
 *       securityScheme="bearerAuth",
 *       type="http",
 *       scheme="bearer",
 *       bearerFormat="JWT"
 *     )
 *   )
 * )
 */
class OpenApi
{
}
