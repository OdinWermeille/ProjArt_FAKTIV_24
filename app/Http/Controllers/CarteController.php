<?php

namespace App\Http\Controllers;

use App\Models\Sentier;
use Inertia\Inertia;

class CarteController extends Controller
{
    function carte() {
        return Inertia::render("Carte");
    }

    function index() {
        $sentiers = Sentier::with(['endroits', 'theme'])->get();
        return response()->json($sentiers);
    }
}

