<?php
// SentierController.php

// SentierController.php

namespace App\Http\Controllers;

use App\Models\Sentier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Theme;
use App\Models\Endroit;

class SentierController extends Controller
{
    public function create()
    {
        $themes = Theme::all();
        $endroits = Endroit::all();
        Log::info('Thématiques et endroits récupérés:', ['themes' => $themes->toArray(), 'endroits' => $endroits->toArray()]);
        return Inertia::render('CreateSentier', [
            'themes' => $themes,
            'endroits' => $endroits,
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Données reçues pour créer un sentier', $request->all());

        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'description' => 'required|string',
                'longueur' => 'required|integer',
                'duree' => 'required|integer',
                'user_id' => 'required|exists:users,id',
                'theme_id' => 'required|exists:themes,id',
                'endroits' => 'required|array', // Validate that endroits is an array
                'endroits.*' => 'exists:endroits,id' // Validate each endroit ID exists
            ]);

            // Handle file upload
            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images'), $imageName);
                $validated['image_url'] = 'images/' . $imageName;
            }

            Log::info('Données validées pour créer un sentier', $validated);

            $sentier = Sentier::create($validated);

            // Attach selected endroits to the sentier
            if (isset($validated['endroits'])) {
                $sentier->endroits()->sync($validated['endroits']);
            }

            return response()->json($sentier, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation: ' . $e->getMessage());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du sentier: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Erreur lors de la création du sentier'], 500);
        }
    }

    public function index()
    {
        $sentiers = Sentier::with('theme')->get();
        return Inertia::render('PageListeSentiers', [
            'sentiers' => $sentiers,
        ]);
    }

    public function show($id)
    {
        $sentier = Sentier::with('theme')->findOrFail($id);
        return response()->json($sentier);
    }
}
