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

        $ingredient = Ingredient::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ingrédient enregistré avec succès dans la base de données !',
                'ingredient' => $ingredient
            ]);
        }

        return redirect()->back()->with('success', 'Ingrédient ajouté avec succès.');
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        if (Auth::user()->role !== 'ADMIN' && $ingredient->utilisateur_id !== Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Action non autorisée.'], 403);
            }
            return abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'phase' => 'nullable|string|max:50',
            'inci' => 'nullable|string|max:255',
        ]);

        $ingredient->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ingrédient mis à jour avec succès.',
                'ingredient' => $ingredient
            ]);
        }

        return redirect()->back()->with('success', 'Ingrédient mis à jour.');
    }

    public function destroy(Ingredient $ingredient)
    {
        if (Auth::user()->role !== 'ADMIN' && $ingredient->utilisateur_id !== Auth::id()) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Action non autorisée.'], 403);
            }
            return abort(403, 'Action non autorisée.');
        }

        $ingredient->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Ingrédient supprimé.']);
        }

        return redirect()->back()->with('success', 'Ingrédient supprimé.');
    }
}

