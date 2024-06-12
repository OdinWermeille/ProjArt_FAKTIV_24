<?php

namespace App\Http\Controllers;

use App\Models\Sentier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Theme;
use App\Models\Endroit;

class SentierController extends Controller
{
    // Affiche la vue pour créer un nouveau sentier
    public function create()
    {
        $themes = Theme::all();
        $endroits = Endroit::all();
        return Inertia::render('CreateSentier', [
            'themes' => $themes,
            'endroits' => $endroits,
        ]);
    }

    // Enregistre un nouveau sentier
    public function store(Request $request)
    {
        // Valide les données de la requête
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'regex:/^[^-\s]+$/'],
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string',
            'longueur' => 'required|numeric',
            'duree' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
            'theme_id' => 'required|exists:themes,id',
            'endroits' => 'required|array|min:2',
            'endroits.*' => 'exists:endroits,id'
        ], [
            // Messages de validation personnalisés
            'nom.required' => 'Le nom est requis.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'nom.regex' => 'Le nom ne doit pas contenir de tirets (-) ou d\'espaces.',
            'image.required' => 'L\'image est requise.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être de type jpeg, png, jpg, gif, ou svg.',
            'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
            'description.required' => 'La description est requise.',
            'longueur.required' => 'La longueur est requise.',
            'longueur.numeric' => 'La longueur doit être un nombre.',
            'duree.required' => 'La durée est requise.',
            'duree.numeric' => 'La durée doit être un nombre.',
            'user_id.required' => 'L\'ID utilisateur est requis.',
            'user_id.exists' => 'L\'ID utilisateur doit exister dans la base de données.',
            'theme_id.required' => 'La thématique est requise.',
            'theme_id.exists' => 'La thématique sélectionnée est invalide.',
            'endroits.required' => 'Veuillez sélectionner au moins deux lieux.',
            'endroits.min' => 'Veuillez sélectionner au moins deux lieux.',
            'endroits.*.exists' => 'Le lieu sélectionné est invalide.',
        ]);

        // Traite le fichier d'image s'il est présent
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('storage/images'), $imageName);
            $validated['image_url'] = 'storage/images/' . $imageName;
        }

        // Crée un nouveau sentier
        $sentier = Sentier::create($validated);
        $sentier->endroits()->sync($validated['endroits']);

        return response()->json($sentier, 201);
    }

    // Affiche la liste des sentiers avec des filtres
    public function index(Request $request)
    {
        $query = Sentier::with('theme');

        // Filtre par activité
        if ($request->filterActivity && $request->filterActivity != 'tout') {
            $query->whereHas('theme', function ($q) use ($request) {
                $q->where('nom', $request->filterActivity);
            });
        }

        // Filtre par distance
        if ($request->filterDistance && $request->filterDistance != 'tout') {
            if ($request->filterDistance == '0-5') {
                $query->where('longueur', '<=', 5);
            } elseif ($request->filterDistance == '6-10') {
                $query->where('longueur', '>', 5)->where('longueur', '<=', 10);
            } elseif ($request->filterDistance == '11+') {
                $query->where('longueur', '>', 10);
            }
        }

        // Tri des résultats
        if ($request->sortOption) {
            if ($request->sortOption === 'newest') {
                $query->orderBy('created_at', 'desc');
            } elseif ($request->sortOption === 'oldest') {
                $query->orderBy('created_at', 'asc');
            }
        }

        $sentiers = $query->get();

        return Inertia::render('PageListeSentiers', [
            'sentiers' => $sentiers,
            'sortOption' => $request->sortOption,
            'filterActivity' => $request->filterActivity,
            'filterDistance' => $request->filterDistance
        ]);
    }

    // Affiche les détails d'un sentier spécifique
    public function show($nom)
    {
        $formattedName = str_replace('-', ' ', $nom);
        $sentier = Sentier::with(['theme', 'endroits'])->where('nom', $formattedName)->firstOrFail();
        return Inertia::render('DetailsSentier', [
            'sentier' => $sentier
        ]);
    }
}
