<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SentierController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CarteController;
use App\Models\Sentier;
use App\Http\Controllers\EndroitController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
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
Route::get('/carte/{id}', [CarteController::class, 'carte']);
Route::get('/carte', [CarteController::class, 'carte']);
Route::get('/carteFetch/sentiers', [CarteController::class, 'index']);
Route::get('/carteFetch/sentier/{id}', [CarteController::class, 'index']);
Route::get('/sentiers', [SentierController::class, 'index']);

Route::get('/sentiers', [SentierController::class, 'index'])->name('sentiers');
Route::post('/api/sentiers', [SentierController::class, 'store']);
Route::get('/sentiers/create', [SentierController::class, 'create']);
Route::post('/sentiers', [SentierController::class, 'store']);
Route::get('/sentiers/{id}', [SentierController::class, 'show']);
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

Route::get('/test', function () {
    return Inertia::render('Test', [
        'sentiers' => Sentier::all()
    ]);
});


Route::get('/sentiers', function () {
    return Inertia::render('PageListeSentiers', [
        'sentiers' => Sentier::with('theme')->get()
    ]);
});

Route::get('/carte/{id}', [CarteController::class, 'carte']);
Route::get('/carte', [CarteController::class, 'carte']);
require __DIR__ . '/auth.php';

Route::get('/lieux/{id}', [EndroitController::class, 'show']);

Route::get('/logout', [AuthenticatedSessionController::class, 'destroy']);
