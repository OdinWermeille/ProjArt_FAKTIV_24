<?php

namespace App\Http\Controllers;

use App\Models\Sentier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Theme;
use App\Models\Endroit;

class SentierController extends Controller
{
    public function create()
    {
        $themes = Theme::all();
        $endroits = Endroit::all();
        return Inertia::render('CreateSentier', [
            'themes' => $themes,
            'endroits' => $endroits,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string',
            'longueur' => 'required|numeric', // Changer integer en numeric
            'duree' => 'required|numeric', // Changer integer en numeric
            'user_id' => 'required|exists:users,id',
            'theme_id' => 'required|exists:themes,id',
            'endroits' => 'required|array',
            'endroits.*' => 'exists:endroits,id'
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validated['image_url'] = 'images/' . $imageName;
        }

        $sentier = Sentier::create($validated);
        $sentier->endroits()->sync($validated['endroits']);

        return response()->json($sentier, 201);
    }

    public function index(Request $request)
    {
        $query = Sentier::with('theme');

        if ($request->filterActivity && $request->filterActivity != 'tout') {
            $query->whereHas('theme', function ($q) use ($request) {
                $q->where('nom', $request->filterActivity);
            });
        }

        if ($request->filterDistance && $request->filterDistance != 'tout') {
            if ($request->filterDistance == '0-5') {
                $query->where('longueur', '<=', 5);
            } elseif ($request->filterDistance == '6-10') {
                $query->where('longueur', '>', 5)->where('longueur', '<=', 10);
            } elseif ($request->filterDistance == '11+') {
                $query->where('longueur', '>', 10);
            }
        }

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

    public function show($id)
    {
        $sentier = Sentier::with(['theme', 'endroits'])->findOrFail($id);
        return Inertia::render('DetailsSentier', [
            'sentier' => $sentier
        ]);
    }
}
