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
            'query'    => collect(['nature', 'city', 'animals', 'food', 'travel',
                                   'architecture', 'people', 'technology'])->random(),
            'per_page' => 20,
            'page'     => rand(1, 10),
        ]);

        if ($pexelsResponse->failed()) {
            return response()->json(['error' => 'No se pudo obtener imagen'], 500);
        }

        $photos = $pexelsResponse->json('photos');
        if (empty($photos)) {
            return response()->json(['error' => 'Sin fotos'], 500);
        }

        $photo    = collect($photos)->random();
        $imageUrl = $photo['src']['large'];
        $imageId  = $photo['id'];

        // 2. Analizar imagen con Azure Computer Vision (API v3.2)
        $endpoint      = rtrim(env('AZURE_VISION_ENDPOINT'), '/');
        $azureResponse = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => env('AZURE_VISION_KEY'),
            'Content-Type'              => 'application/json',
        ])->post("{$endpoint}/vision/v3.2/analyze?visualFeatures=Tags&language=es", [
            'url' => $imageUrl,
        ]);

        // Extraer solo palabras sueltas (sin espacios) con confianza > 0.7
        $tags = [];
        if ($azureResponse->successful()) {
            $rawTags = $azureResponse->json('tags') ?? [];
            $tags = collect($rawTags)
                ->filter(fn($t) => $t['confidence'] > 0.7 && !str_contains($t['name'], ' '))
                ->pluck('name')
                ->values()
                ->toArray();
        }

        // Fallback: primera palabra del alt de Pexels
        if (empty($tags)) {
            $firstWord = explode(' ', $photo['alt'] ?? 'imagen')[0];
            $tags      = [strtolower($firstWord)];
        }

        $mainAnswer = $tags[0];

        // Hint 2: número de letras (no revela la respuesta)
        $hint2 = 'La palabra tiene ' . mb_strlen($mainAnswer) . ' letras';

        return response()->json([
            'image_url' => $imageUrl,
            'image_id'  => $imageId,
            'answers'   => $tags,
            'hint2'     => $hint2,
        ]);
    }

    public function saveScore(Request $request)
    {
        $request->validate([
            'points'         => 'required|integer|min:0',
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
