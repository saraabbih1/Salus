<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Salus API",
 *     version="1.0.0",
 *     description="API documentation for the Salus health assistant application."
 * )
 *
 * @OA\Server(
 *     url="http://localhost",
 *     description="Application server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Use a Sanctum bearer token. Example: Bearer 1|abcdef..."
 * )
 */
class SwaggerInfo
{
}
