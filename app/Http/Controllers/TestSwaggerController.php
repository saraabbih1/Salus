<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class TestSwaggerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/test-swagger",
     *     summary="Test Swagger",
     *     tags={"Test"},
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function test()
    {
        return response()->json([
            "success" => true,
            "message" => "Swagger works"
        ]);
    }
}
