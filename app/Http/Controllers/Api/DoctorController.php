<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class DoctorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/doctors",
     *     operationId="listDoctors",
     *     tags={"Doctors"},
     *     summary="List all doctors",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Doctors retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Dr. Youssef Amrani"),
     *                     @OA\Property(property="specialty", type="string", example="Cardiology"),
     *                     @OA\Property(property="city", type="string", example="Casablanca"),
     *                     @OA\Property(property="yearsofexperience", type="integer", example=12),
     *                     @OA\Property(property="consultation_price", type="number", format="float", example=350.00),
     *                     @OA\Property(property="available_days", type="array", @OA\Items(type="string", example="Monday"))
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Doctor::all(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/doctors/{id}",
     *     operationId="showDoctor",
     *     tags={"Doctors"},
     *     summary="Get a doctor by ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Doctor ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Doctor retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Dr. Youssef Amrani"),
     *                 @OA\Property(property="specialty", type="string", example="Cardiology"),
     *                 @OA\Property(property="city", type="string", example="Casablanca"),
     *                 @OA\Property(property="yearsofexperience", type="integer", example=12),
     *                 @OA\Property(property="consultation_price", type="number", format="float", example=350.00),
     *                 @OA\Property(property="available_days", type="array", @OA\Items(type="string", example="Monday"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Doctor not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Doctor not found."))
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $doctor = Doctor::find($id);

        if (! $doctor) {
            return response()->json(['message' => 'Doctor not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $doctor,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/doctors/search",
     *     operationId="searchDoctors",
     *     tags={"Doctors"},
     *     summary="Search doctors by specialty or city",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="specialty",
     *         in="query",
     *         required=false,
     *         description="Filter by specialty",
     *         @OA\Schema(type="string", example="Cardiology")
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         required=false,
     *         description="Filter by city",
     *         @OA\Schema(type="string", example="Casablanca")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Doctors search completed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Dr. Youssef Amrani"),
     *                     @OA\Property(property="specialty", type="string", example="Cardiology"),
     *                     @OA\Property(property="city", type="string", example="Casablanca"),
     *                     @OA\Property(property="yearsofexperience", type="integer", example=12),
     *                     @OA\Property(property="consultation_price", type="number", format="float", example=350.00),
     *                     @OA\Property(property="available_days", type="array", @OA\Items(type="string", example="Monday"))
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $query = Doctor::query();

        if ($request->filled('specialty')) {
            $query->where('specialty', 'like', '%' . $request->string('specialty') . '%');
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->string('city') . '%');
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }
}
