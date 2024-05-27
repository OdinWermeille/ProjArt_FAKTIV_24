<?php

namespace App\Http\Controllers;

use App\Models\Sentier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Theme;

class SentierController extends Controller
{
    public function create()
    {
        $themes = Theme::all();
        Log::info('Thématiques récupérées:', $themes->toArray());
        return Inertia::render('CreateSentier', [
            'themes' => $themes,
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Données reçues pour créer un sentier', $request->all());
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'image_url' => 'required|url',
                'description' => 'required|string',
                'longueur' => 'required|integer',
                'duree' => 'required|integer',
                'user_id' => 'required|exists:users,id',
                'theme_id' => 'required|exists:themes,id',
            ]);

            Log::info('Données validées pour créer un sentier', $validated);

            $sentier = Sentier::create($validated);

            return response()->json($sentier, 201);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du sentier: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Erreur lors de la création du sentier'], 500);
        }
    }

    public function index()
    {
        $sentiers = Sentier::all();
        return response()->json($sentiers);
    }
}
