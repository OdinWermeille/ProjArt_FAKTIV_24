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
            ], [
                'nom.required' => 'Le nom est requis.',
                'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
                'description.required' => 'La description est requise.',
                'localite.required' => 'La localité est requise.',
                'coordonneesX.required' => 'Les coordonnées X sont requises.',
                'coordonneesX.numeric' => 'Les coordonnées X doivent être un nombre.',
                'coordonneesX.between' => 'Les coordonnées X doivent être comprises entre -180 et 180.',
                'coordonneesY.required' => 'Les coordonnées Y sont requises.',
                'coordonneesY.numeric' => 'Les coordonnées Y doivent être un nombre.',
                'coordonneesY.between' => 'Les coordonnées Y doivent être comprises entre -90 et 90.',
                'image.required' => 'L\'image est requise.',
                'image.image' => 'Le fichier doit être une image.',
                'image.mimes' => 'L\'image doit être de type jpeg, png, jpg, gif, ou svg.',
                'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
                'user_id.required' => 'L\'ID utilisateur est requis.',
                'user_id.exists' => 'L\'ID utilisateur doit exister dans la base de données.',
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
