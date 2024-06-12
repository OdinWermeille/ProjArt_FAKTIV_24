<?php

namespace App\Http\Controllers;

use App\Models\Sentier;
use Inertia\Inertia;

class CarteController extends Controller
{
    // Fonction pour afficher la vue "Carte"
    function carte()
    {
        return Inertia::render("Carte");
    }

    // Fonction pour récupérer et renvoyer tous les sentiers avec leurs relations "endroits" et "theme"
    function index()
    {
        $sentiers = Sentier::with(['endroits', 'theme'])->get();
        return response()->json($sentiers);
    }
}
