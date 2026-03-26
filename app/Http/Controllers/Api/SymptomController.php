<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Symptom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class SymptomController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/symptoms",
     *     operationId="listSymptoms",
     *     tags={"Symptoms"},
     *     summary="List the authenticated user's symptoms",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Symptoms retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Headache"),
     *                     @OA\Property(property="severity", type="string", example="moderate"),
     *                     @OA\Property(property="description", type="string", example="Throbbing pain on the left side."),
     *                     @OA\Property(property="date_recorded", type="string", format="date", example="2026-03-26"),
     *                     @OA\Property(property="notes", type="string", example="Gets worse in bright light."),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-26T08:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-03-26T08:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $symptoms = Symptom::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $symptoms,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/symptoms",
     *     operationId="storeSymptom",
     *     tags={"Symptoms"},
     *     summary="Create a new symptom record",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","severity","date_recorded"},
     *             @OA\Property(property="name", type="string", example="Headache"),
     *             @OA\Property(property="severity", type="string", enum={"mild","moderate","severe"}, example="moderate"),
     *             @OA\Property(property="description", type="string", example="Throbbing pain on the left side."),
     *             @OA\Property(property="date_recorded", type="string", format="date", example="2026-03-26"),
     *             @OA\Property(property="notes", type="string", example="Started after skipping lunch.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Symptom created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Symptom created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Headache"),
     *                 @OA\Property(property="severity", type="string", example="moderate"),
     *                 @OA\Property(property="description", type="string", example="Throbbing pain on the left side."),
     *                 @OA\Property(property="date_recorded", type="string", format="date", example="2026-03-26"),
     *                 @OA\Property(property="notes", type="string", example="Started after skipping lunch."),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-26T08:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2026-03-26T08:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="The given data was invalid."))
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'severity' => ['required', Rule::in(['mild', 'moderate', 'severe'])],
            'description' => ['nullable', 'string'],
            'date_recorded' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $symptom = Symptom::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Symptom created successfully.',
            'data' => $symptom,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/symptoms/{id}",
     *     operationId="showSymptom",
     *     tags={"Symptoms"},
     *     summary="Get a single symptom",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Symptom ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Symptom retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Headache"),
     *                 @OA\Property(property="severity", type="string", example="moderate"),
     *                 @OA\Property(property="description", type="string", example="Throbbing pain on the left side."),
     *                 @OA\Property(property="date_recorded", type="string", format="date", example="2026-03-26"),
     *                 @OA\Property(property="notes", type="string", example="Gets worse in bright light.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Symptom not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Symptom not found."))
     *     )
     * )
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $symptom = Symptom::where('user_id', $request->user()->id)->find($id);

        if (! $symptom) {
            return response()->json(['message' => 'Symptom not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $symptom,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/symptoms/{id}",
     *     operationId="updateSymptom",
     *     tags={"Symptoms"},
     *     summary="Update a symptom",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Symptom ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Headache"),
     *             @OA\Property(property="severity", type="string", enum={"mild","moderate","severe"}, example="severe"),
     *             @OA\Property(property="description", type="string", example="Pain is stronger than yesterday."),
     *             @OA\Property(property="date_recorded", type="string", format="date", example="2026-03-26"),
     *             @OA\Property(property="notes", type="string", example="Taking rest and drinking water.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Symptom updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Symptom updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Headache"),
     *                 @OA\Property(property="severity", type="string", example="severe"),
     *                 @OA\Property(property="description", type="string", example="Pain is stronger than yesterday."),
     *                 @OA\Property(property="date_recorded", type="string", format="date", example="2026-03-26"),
     *                 @OA\Property(property="notes", type="string", example="Taking rest and drinking water.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Symptom not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Symptom not found."))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="The given data was invalid."))
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $symptom = Symptom::where('user_id', $request->user()->id)->find($id);

        if (! $symptom) {
            return response()->json(['message' => 'Symptom not found.'], 404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'severity' => ['sometimes', Rule::in(['mild', 'moderate', 'severe'])],
            'description' => ['nullable', 'string'],
            'date_recorded' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $symptom->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Symptom updated successfully.',
            'data' => $symptom->fresh(),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/symptoms/{id}",
     *     operationId="deleteSymptom",
     *     tags={"Symptoms"},
     *     summary="Delete a symptom",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Symptom ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Symptom deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Symptom deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Symptom not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Symptom not found."))
     *     )
     * )
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $symptom = Symptom::where('user_id', $request->user()->id)->find($id);

        if (! $symptom) {
            return response()->json(['message' => 'Symptom not found.'], 404);
        }

        $symptom->delete();

        return response()->json([
            'success' => true,
            'message' => 'Symptom deleted successfully.',
        ]);
    }
}
