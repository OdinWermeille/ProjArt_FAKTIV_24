<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CarteController extends Controller
{
    function carte($id = null) {
        if ($id) {
            return Inertia::render("Carte")->with('id', $id);
        }
        return Inertia::render("Carte");
    }
}
