<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Symptom;
use App\Models\AiAdvice;

class AIHealthAdviceController extends Controller
{
    public function generate(Request $request)
    {
        $user = $request->user();

        //  récupérer les symptômes récents
        $symptoms = Symptom::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->pluck('name')
            ->toArray();

        if (empty($symptoms)) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun symptôme trouvé'
            ], 400);
        }

        //  construire prompt
        $symptomsText = implode(", ", $symptoms);

        $prompt = "User symptoms: $symptomsText. Provide general wellness advice, not medical diagnosis.";

        //  appel API Openai
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
        ]);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur API IA'
            ], 500);
        }

        $advice = $response['choices'][0]['message']['content'];
         //  sauvegarder en base
        $aiAdvice = AiAdvice::create([
            'user_id' => $user->id,
            'advice' => $advice,
            'symptoms_used' => json_encode($symptoms),
            'generated_at' => now(),
        ]);

        //  retourner réponse
        return response()->json([
            'success' => true,
            'data' => [
                'advice' => $aiAdvice->advice,
                'generated_at' => $aiAdvice->generated_at,
            ],
            'message' => 'Conseil généré avec succès'
        ]);
    }

    // history
    public function index(Request $request)
    {
        $advices = AiAdvice::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $advices,
            'message' => 'Historique récupéré'
        ]);
    }
}

        