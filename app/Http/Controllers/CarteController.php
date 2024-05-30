<?php

namespace App\Http\Controllers;

use App\Models\Sentier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarteController extends Controller
{
    function carte() {
        return Inertia::render("Carte");
    }

    function index($id = null) {
        if ($id != null) {
            $sentier = Sentier::with('endroits')->findOrFail($id);
            return response()->json($sentier);
        }
        $sentiers = Sentier::with('endroits')->get();
        return response()->json($sentiers);
    }
}

