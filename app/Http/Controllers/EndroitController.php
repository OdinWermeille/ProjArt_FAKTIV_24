<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Endroit;

class EndroitController extends Controller
{
    public function create()
    {
        return Inertia::render('CreateEndroit');
    }

    public function store(Request $request)
    {
        Log::info('Données reçues pour créer un lieu', $request->all());

        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'description' => 'required|string',
                'localite' => 'required|string',
                'coordonneesX' => 'required|numeric|between:-180,180',
                'coordonneesY' => 'required|numeric|between:-90,90',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'user_id' => 'required|exists:users,id',
            ]);

            // Handle file upload
            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images'), $imageName);
                $validated['image_url'] = 'images/' . $imageName;
            }

            Log::info('Données validées pour créer un lieu', $validated);

            $endroit = Endroit::create($validated);

            return response()->json($endroit, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation: ' . $e->getMessage());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du lieu: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Erreur lors de la création du lieu'], 500);
        }
    }

    public function show($id)
    {
        $endroit = Endroit::findOrFail($id);
        return Inertia::render('DetailEndroit', [
            'endroit' => $endroit,
        ]);
    }
}
