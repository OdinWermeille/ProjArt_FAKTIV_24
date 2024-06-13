<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SentierController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CarteController;
use App\Http\Controllers\EndroitController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Log;

// Redirige la route racine vers la page des sentiers
Route::redirect('/', '/sentiers')->name('sentiers');

// Groupe de routes nécessitant une authentification et une vérification par e-mail
Route::middleware(['auth', 'verified'])->group(function () {
    // Routes pour l'édition, la mise à jour et la suppression du profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Routes pour la création et le stockage des sentiers
    Route::get('/sentiers/create', [SentierController::class, 'create'])->name('sentiers.create');
    Route::post('/sentiers', [SentierController::class, 'store']);
    
    // Routes pour la création et le stockage des endroits
    Route::get('/lieux/create', [EndroitController::class, 'create'])->name('endroits.create');
    Route::post('/lieux', [EndroitController::class, 'store']);
    
    // Route pour vérifier l'authentification de l'utilisateur
    Route::get('/user', function () {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::user()
        ]);
    });
});

// Routes pour afficher la carte
Route::get('/carte', [CarteController::class, 'carte']);
//Route pour afficher la carte des sentiers
Route::get('/carteFetch/sentiers', [CarteController::class, 'index']);
//Route pour afficher la liste des sentiers
Route::get('/sentiers', [SentierController::class, 'index'])->name('sentiers');
//Route pour stocker un sentier via l'API
Route::post('/api/sentiers', [SentierController::class, 'store']);
// Route pour créer un sentier
Route::get('/sentiers/create', [SentierController::class, 'create']);
// Route pour stocker un sentier
Route::post('/sentiers', [SentierController::class, 'store']);
// Route pour afficher un sentier spécifique
Route::get('/sentiers/{nom}', [SentierController::class, 'show']);
// Route pour afficher le formulaire de modification d'un sentier
Route::get('/sentiers/{nom}/edit', [SentierController::class, 'edit'])->name('sentiers.edit');
// Route pour soumettre les modifications d'un sentier
Route::put('/api/sentiers/{id}', [SentierController::class, 'update'])->name('sentiers.update');
// Route pour supprimer un sentier
Route::delete('/api/sentiers/{id}', [SentierController::class, 'destroy']);
// Route pour stocker un endroit via l'API
Route::post('/api/endroits', [EndroitController::class, 'store']);

// Route pour récupérer toutes les thématiques via l'API
Route::get('/api/themes', function () {
    return response()->json(App\Models\Theme::all());
});

// Route pour récupérer tous les endroits via l'API
Route::get('/api/endroits', function () {
    return response()->json(App\Models\Endroit::all());
});

// Route pour vérifier l'authentification de l'utilisateur via l'API
Route::get('/api/user', function () {
    return response()->json([
        'authenticated' => Auth::check(),
        'user' => Auth::user()
    ]);
});

// Route pour la déconnexion de l'utilisateur
Route::get('/logout', [AuthenticatedSessionController::class, 'destroy']);

// Inclusion des routes d'authentification générées par Laravel
require __DIR__ . '/auth.php';

// Route pour afficher un endroit spécifique
Route::get('/lieux/{nom}', [EndroitController::class, 'show']);