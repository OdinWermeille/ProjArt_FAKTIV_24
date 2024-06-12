<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Endroit;

class EndroitController extends Controller
{
    // Affiche la vue pour créer un nouveau lieu
    public function create()
    {
        return Inertia::render('CreateEndroit');
    }

    // Enregistre un nouveau lieu
    public function store(Request $request)
    {
        try {
            // Valide les données de la requête
            $validated = $request->validate([
                'nom' => ['required', 'string', 'max:255', 'unique:endroits,nom', 'regex:/^[^\-]+$/'],
                'description' => 'required|string',
                'localite' => 'required|string',
                'coordonneesX' => 'required|numeric|between:-180,180',
                'coordonneesY' => 'required|numeric|between:-90,90',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'user_id' => 'required|exists:users,id',
            ], [
                // Messages de validation personnalisés
                'nom.required' => 'Le nom est requis.',
                'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
                'nom.unique' => 'Ce lieu existe déjà. Veuillez en choisir un autre.',
                'nom.regex' => 'Le nom ne doit pas contenir de tirets (-).',
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

            // Traite le fichier d'image s'il est présent
            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('storage/images'), $imageName);
                $validated['image_url'] = 'storage/images/' . $imageName;
            }

            // Crée un nouveau lieu
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

    // Affiche les détails d'un lieu spécifique
    public function show($nom)
    {
        $formattedName = str_replace('-', ' ', $nom);
        $endroit = Endroit::where('nom', $formattedName)->firstOrFail();
        return Inertia::render('DetailEndroit', [
            'endroit' => $endroit,
        ]);
    }
}
