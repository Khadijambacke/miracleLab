<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formule;
use App\Models\FormuleIngredient;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Auth;

class FormuleController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'categorie' => 'nullable|string|max:255',
            'type_produit' => 'nullable|string|max:255',
            'poids_lot' => 'nullable|numeric',
            'poids_total_lot' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'description' => 'nullable|string',
            'ph_estime' => 'nullable|numeric',
            'ingredients' => 'required|array',
        ]);

        $poidsLot = $validated['poids_lot'] ?? $validated['poids_total_lot'] ?? 1000.00;
        $notes = $validated['notes'] ?? $validated['description'] ?? null;

        $formule = Formule::create([
            'utilisateur_id' => Auth::id(),
            'nom' => $validated['nom'],
            'categorie' => $validated['categorie'] ?? 'haircare',
            'type_produit' => $validated['type_produit'] ?? 'LEAVE_IN',
            'poids_lot' => $poidsLot,
            'notes' => $notes,
            'ph_estime' => $validated['ph_estime'] ?? null,
        ]);

        foreach ($validated['ingredients'] as $ing) {
            $nomIng = $ing['nom_ingredient_custom'] ?? $ing['name'] ?? $ing['nom'] ?? null;
            if (!$nomIng) continue;

            $pct = floatval($ing['pourcentage'] ?? $ing['pct'] ?? 0);
            $phase = $ing['phase'] ?? 'AQUEUSE';
            $coutParKg = floatval($ing['cout_par_kg'] ?? $ing['cost'] ?? 0);

            // Find or create ingredient in database
            $ingredientObj = Ingredient::firstOrCreate(
                ['nom' => $nomIng],
                [
                    'phase' => $phase,
                    'utilisateur_id' => Auth::id(),
                    'est_personnalise' => true
                ]
            );

            FormuleIngredient::create([
                'formule_id' => $formule->id,
                'ingredient_id' => $ingredientObj->id,
                'pourcentage' => $pct,
                'phase' => $phase,
                'grammes_calculs' => ($poidsLot * $pct) / 100,
                'cout_par_kg' => $coutParKg,
            ]);
        }

        return response()->json([
            'message' => 'Formule sauvegardée avec succès !',
            'formule' => $formule->load('formuleIngredients.ingredient')
        ]);
    }

    public function update(Request $request, Formule $formule)
    {
        if (Auth::user()->role !== 'ADMIN' && $formule->utilisateur_id !== Auth::id()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Action non autorisée.'], 403);
            }
            return abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'categorie' => 'nullable|string|max:255',
            'type_produit' => 'nullable|string|max:255',
            'poids_lot' => 'nullable|numeric',
            'poids_total_lot' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'description' => 'nullable|string',
            'ph_estime' => 'nullable|numeric',
            'ingredients' => 'nullable|array',
        ]);

        $poidsLot = $validated['poids_lot'] ?? $validated['poids_total_lot'] ?? $formule->poids_lot;
        $notes = $validated['notes'] ?? $validated['description'] ?? $formule->notes;

        $formule->update([
            'nom' => $validated['nom'] ?? $formule->nom,
            'categorie' => $validated['categorie'] ?? $formule->categorie,
            'type_produit' => $validated['type_produit'] ?? $formule->type_produit,
            'poids_lot' => $poidsLot,
            'notes' => $notes,
            'ph_estime' => $validated['ph_estime'] ?? $formule->ph_estime,
        ]);

        if (isset($validated['ingredients'])) {
            FormuleIngredient::where('formule_id', $formule->id)->delete();

            foreach ($validated['ingredients'] as $ing) {
                $nomIng = $ing['nom_ingredient_custom'] ?? $ing['name'] ?? $ing['nom'] ?? null;
                if (!$nomIng) continue;

                $pct = floatval($ing['pourcentage'] ?? $ing['pct'] ?? 0);
                $phase = $ing['phase'] ?? 'AQUEUSE';
                $coutParKg = floatval($ing['cout_par_kg'] ?? $ing['cost'] ?? 0);

                $ingredientObj = Ingredient::firstOrCreate(
                    ['nom' => $nomIng],
                    [
                        'phase' => $phase,
                        'utilisateur_id' => Auth::id(),
                        'est_personnalise' => true
                    ]
                );

                FormuleIngredient::create([
                    'formule_id' => $formule->id,
                    'ingredient_id' => $ingredientObj->id,
                    'pourcentage' => $pct,
                    'phase' => $phase,
                    'grammes_calculs' => ($poidsLot * $pct) / 100,
                    'cout_par_kg' => $coutParKg,
                ]);
            }
        }

        return response()->json([
            'message' => 'Formule mise à jour avec succès !',
            'formule' => $formule->load('formuleIngredients.ingredient')
        ]);
    }

    public function destroy(Formule $formule)
    {
        if (Auth::user()->role !== 'ADMIN' && $formule->utilisateur_id !== Auth::id()) {
            return abort(403, 'Action non autorisée.');
        }

        $formule->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Formule supprimée avec succès.']);
        }

        return redirect()->back()->with('success', 'Formule supprimée.');
    }
}

