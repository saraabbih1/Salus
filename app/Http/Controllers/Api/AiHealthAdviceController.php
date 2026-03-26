<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiHealthAdvice;
use App\Models\Symptom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;

class AIHealthAdviceController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/ai/health-advice",
     *     operationId="generateHealthAdvice",
     *     tags={"AI"},
     *     summary="Generate AI health advice",
     *     description="Generates general wellness advice based on the authenticated user's recent symptoms.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="additional_context", type="string", example="I have also been feeling tired for two days.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Health advice generated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Health advice generated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="advice", type="string", example="Stay hydrated, rest well, and monitor your symptoms. If they worsen, contact a doctor."),
     *                 @OA\Property(property="symptoms", type="string", example="Headache, Fatigue"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-26T11:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No symptoms found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No symptoms found for this user.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="AI service error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unable to generate health advice at the moment.")
     *         )
     *     )
     * )
     */
    public function generate(Request $request): JsonResponse
    {
        $symptoms = Symptom::where('user_id', $request->user()->id)
            ->latest()
            ->take(5)
            ->pluck('name')
            ->values()
            ->all();

        if ($symptoms === []) {
            return response()->json([
                'success' => false,
                'message' => 'No symptoms found for this user.',
            ], 400);
        }

        $additionalContext = $request->string('additional_context')->trim()->toString();
        $prompt = 'User symptoms: ' . implode(', ', $symptoms) . '. Provide general wellness advice only, not a diagnosis.';

        if ($additionalContext !== '') {
            $prompt .= ' Additional context: ' . $additionalContext . '.';
        }

        $response = Http::withToken((string) env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if (! $response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to generate health advice at the moment.',
            ], 500);
        }

        $adviceText = data_get($response->json(), 'choices.0.message.content', '');

        $record = AiHealthAdvice::create([
            'user_id' => $request->user()->id,
            'advice' => $adviceText,
            'symptoms' => json_encode($symptoms),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Health advice generated successfully.',
            'data' => $record,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/ai/history",
     *     operationId="listHealthAdviceHistory",
     *     tags={"AI"},
     *     summary="Get AI advice history",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="AI advice history retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="AI advice history retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="advice", type="string", example="Stay hydrated, rest well, and monitor your symptoms."),
     *                     @OA\Property(property="symptoms", type="string", example="Headache, Fatigue"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-26T11:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-03-26T11:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function history(Request $request): JsonResponse
    {
        $history = AiHealthAdvice::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'AI advice history retrieved successfully.',
            'data' => $history,
        ]);
    }
}
