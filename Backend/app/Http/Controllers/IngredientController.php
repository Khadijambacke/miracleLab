<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Auth;

class IngredientController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'phase' => 'nullable|string|max:50',
            'inci' => 'nullable|string|max:255',
        ]);

        $validated['utilisateur_id'] = Auth::user()->role === 'ADMIN' ? null : Auth::id();
        $validated['est_personnalise'] = Auth::user()->role !== 'ADMIN';

        Ingredient::create($validated);

        return redirect()->back()->with('success', 'Ingrédient ajouté avec succès.');
    }

    public function destroy(Ingredient $ingredient)
    {
        if (Auth::user()->role !== 'ADMIN' && $ingredient->utilisateur_id !== Auth::id()) {
            return abort(403, 'Action non autorisée.');
        }

        $ingredient->delete();

        return redirect()->back()->with('success', 'Ingrédient supprimé.');
    }
}
