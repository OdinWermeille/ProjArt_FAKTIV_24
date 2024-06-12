<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SentierController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CarteController;
use App\Http\Controllers\EndroitController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Log;

Route::redirect('/', '/sentiers')->name('sentiers');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/sentiers/create', [SentierController::class, 'create'])->name('sentiers.create');
    Route::post('/sentiers', [SentierController::class, 'store']);
    Route::get('/lieux/create', [EndroitController::class, 'create'])->name('endroits.create');
    Route::post('/lieux', [EndroitController::class, 'store']);
    Route::get('/user', function () {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::user()
        ]);
    });
});

Route::get('/carte', [CarteController::class, 'carte']);
Route::get('/carteFetch/sentiers', [CarteController::class, 'index']);
Route::get('/sentiers', [SentierController::class, 'index'])->name('sentiers');
Route::post('/api/sentiers', [SentierController::class, 'store']);
Route::get('/sentiers/create', [SentierController::class, 'create']);
Route::post('/sentiers', [SentierController::class, 'store']);

Route::get('/sentiers/{nom}', [SentierController::class, 'show']);

// Route pour afficher le formulaire de modification
Route::get('/sentiers/{nom}/edit', [SentierController::class, 'edit'])->name('sentiers.edit');

// Route pour soumettre les modifications
Route::put('/api/sentiers/{id}', [SentierController::class, 'update'])->name('sentiers.update');

Route::delete('/api/sentiers/{id}', [SentierController::class, 'destroy']);

Route::post('/api/endroits', [EndroitController::class, 'store']);
Route::get('/api/themes', function () {
    return response()->json(App\Models\Theme::all());
});
Route::get('/api/endroits', function () {
    return response()->json(App\Models\Endroit::all());
});
Route::get('/api/user', function () {
    return response()->json([
        'authenticated' => Auth::check(),
        'user' => Auth::user()
    ]);
});

Route::get('/logout', [AuthenticatedSessionController::class, 'destroy']);

require __DIR__ . '/auth.php';

Route::get('/lieux/{nom}', [EndroitController::class, 'show']);
