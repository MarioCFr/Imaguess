<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GameController extends Controller
{
    public function nextImage()
    {
        // 1. Obtener imagen aleatoria de Pexels
        $pexelsResponse = Http::withHeaders([
            'Authorization' => env('PEXELS_API_KEY'),
        ])->get('https://api.pexels.com/v1/search', [
            'query'       => collect(['nature', 'city', 'animals', 'food', 'travel',
                                      'architecture', 'people', 'technology'])->random(),
            'per_page'    => 20,
            'page'        => rand(1, 10),
        ]);

        if ($pexelsResponse->failed()) {
            return response()->json(['error' => 'No se pudo obtener imagen'], 500);
        }

        $photos = $pexelsResponse->json('photos');
        if (empty($photos)) {
            return response()->json(['error' => 'Sin fotos'], 500);
        }

        $photo     = collect($photos)->random();
        $imageUrl  = $photo['src']['large'];
        $imageId   = $photo['id'];

        // 2. Analizar imagen con Azure Computer Vision
        $azureResponse = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => env('AZURE_VISION_KEY'),
            'Content-Type'              => 'application/json',
        ])->post(env('AZURE_VISION_ENDPOINT') . 'computervision/imageanalysis:analyze', [
            'url' => $imageUrl,
        ], [
            'api-version' => '2023-02-01-preview',
            'features'    => 'tags',
            'language'    => 'es',
        ]);

        // Extraer tags válidos (confianza > 0.7)
        $tags = [];
        if ($azureResponse->successful()) {
            $rawTags = $azureResponse->json('tagsResult.values') ?? [];
            $tags = collect($rawTags)
                ->filter(fn($t) => $t['confidence'] > 0.7)
                ->pluck('name')
                ->values()
                ->toArray();
        }

        // Si Azure falla o no hay tags, usar la categoría de Pexels como fallback
        if (empty($tags)) {
            $tags = [$photo['alt'] ?? 'imagen'];
        }

        return response()->json([
            'image_url' => $imageUrl,
            'image_id'  => $imageId,
            'answers'   => $tags,        // array de respuestas válidas
            'hint1'     => $tags[0][0] . str_repeat('_', strlen($tags[0]) - 2) . $tags[0][strlen($tags[0]) - 1],
            'hint2'     => 'Categoría: ' . strtoupper($tags[0]),
        ]);
    }

    public function saveScore(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:0',
            'images_guessed' => 'required|integer|min:0',
        ]);

        Score::create([
            'user_id'        => auth()->id(),
            'points'         => $request->points,
            'images_guessed' => $request->images_guessed,
        ]);

        return response()->json(['ok' => true]);
    }
}
