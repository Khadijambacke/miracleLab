<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formule;
use App\Models\FormuleIngredient;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\FormulaCalculatorService;

class FormuleController extends Controller
{
    protected FormulaCalculatorService $calculatorService;

    public function __construct(FormulaCalculatorService $calculatorService)
    {
        $this->calculatorService = $calculatorService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'categorie' => 'nullable|string|max:255',
            'type_produit' => 'nullable|string|max:255',
            'poids_lot' => 'nullable|numeric|min:0',
            'poids_total_lot' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'description' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
        ]);

        $poidsLot = $validated['poids_lot'] ?? $validated['poids_total_lot'] ?? 1000.00;
        $notes = $validated['notes'] ?? $validated['description'] ?? null;
        $typeProduit = $validated['type_produit'] ?? 'LEAVE_IN';
        
        // Normaliser les ingrédients pour le service
        $ingredientsInput = [];
        foreach ($validated['ingredients'] as $ing) {
            $nomIng = $ing['nom_ingredient_custom'] ?? $ing['name'] ?? $ing['nom'] ?? null;
            if (!$nomIng) continue;
            
            $ingredientsInput[] = [
                'nom_ingredient' => $nomIng,
                'pourcentage' => floatval($ing['pourcentage'] ?? $ing['pct'] ?? 0),
                'phase' => $ing['phase'] ?? 'AQUEUSE',
                'cout_par_kg' => floatval($ing['cout_par_kg'] ?? $ing['cost'] ?? 0),
            ];
        }

        // Validation 1: Règle des 100%
        $rule100 = $this->calculatorService->validateRuleOf100($ingredientsInput, $typeProduit);
        if (!$rule100['valid']) {
            return response()->json(['message' => $rule100['message'], 'errors' => ['ingredients' => [$rule100['message']]]], 422);
        }

        // Validation 2: Limites de dosage
        $limits = $this->calculatorService->validateDosageLimits($ingredientsInput);
        if (!$limits['valid']) {
            return response()->json(['message' => 'Limites de dosage dépassées.', 'errors' => ['ingredients' => $limits['messages']]], 422);
        }

        // Validation 3: Compatibilité chimique
        $compat = $this->calculatorService->validateCompatibility($ingredientsInput, $typeProduit);
        if (!$compat['valid']) {
            return response()->json(['message' => 'Incompatibilité chimique détectée.', 'errors' => ['ingredients' => $compat['messages']]], 422);
        }

        // Recalcul des valeurs fiables côté serveur
        $phEstime = $this->calculatorService->estimatePH($ingredientsInput, $typeProduit);
        $this->calculatorService->calculateGrams($ingredientsInput, $poidsLot);

        try {
            $formule = DB::transaction(function () use ($validated, $poidsLot, $notes, $typeProduit, $phEstime, $ingredientsInput) {
                $formule = Formule::create([
                    'utilisateur_id' => Auth::id(),
                    'nom' => $validated['nom'],
                    'categorie' => $validated['categorie'] ?? 'haircare',
                    'type_produit' => $typeProduit,
                    'poids_lot' => $poidsLot,
                    'notes' => $notes,
                    'ph_estime' => $phEstime, // Écrasé par le calcul serveur, on ignore celui du frontend
                ]);

                foreach ($ingredientsInput as $ing) {
                    $ingredientObj = Ingredient::firstOrCreate(
                        ['nom' => $ing['nom_ingredient']],
                        [
                            'phase' => $ing['phase'],
                            'utilisateur_id' => Auth::id(),
                            'est_personnalise' => true
                        ]
                    );

                    FormuleIngredient::create([
                        'formule_id' => $formule->id,
                        'ingredient_id' => $ingredientObj->id,
                        'pourcentage' => $ing['pourcentage'],
                        'phase' => $ing['phase'],
                        'grammes_calculs' => $ing['grammes'],
                        'cout_par_kg' => $ing['cout_par_kg'],
                    ]);
                }

                return $formule;
            });

            return response()->json([
                'message' => 'Formule sauvegardée avec succès !',
                'formule' => $formule->load('formuleIngredients.ingredient')
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'enregistrement de la formule.', 'error' => $e->getMessage()], 500);
        }
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
            'poids_lot' => 'nullable|numeric|min:0',
            'poids_total_lot' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|array',
        ]);

        $poidsLot = $validated['poids_lot'] ?? $validated['poids_total_lot'] ?? $formule->poids_lot;
        $notes = $validated['notes'] ?? $validated['description'] ?? $formule->notes;
        $typeProduit = $validated['type_produit'] ?? $formule->type_produit;

        // Si on met à jour les ingrédients, on valide la logique métier
        if (isset($validated['ingredients'])) {
            $ingredientsInput = [];
            foreach ($validated['ingredients'] as $ing) {
                $nomIng = $ing['nom_ingredient_custom'] ?? $ing['name'] ?? $ing['nom'] ?? null;
                if (!$nomIng) continue;
                
                $ingredientsInput[] = [
                    'nom_ingredient' => $nomIng,
                    'pourcentage' => floatval($ing['pourcentage'] ?? $ing['pct'] ?? 0),
                    'phase' => $ing['phase'] ?? 'AQUEUSE',
                    'cout_par_kg' => floatval($ing['cout_par_kg'] ?? $ing['cost'] ?? 0),
                ];
            }

            // Validations Métier
            $rule100 = $this->calculatorService->validateRuleOf100($ingredientsInput, $typeProduit);
            if (!$rule100['valid']) {
                return response()->json(['message' => $rule100['message'], 'errors' => ['ingredients' => [$rule100['message']]]], 422);
            }

            $limits = $this->calculatorService->validateDosageLimits($ingredientsInput);
            if (!$limits['valid']) {
                return response()->json(['message' => 'Limites de dosage dépassées.', 'errors' => ['ingredients' => $limits['messages']]], 422);
            }

            $compat = $this->calculatorService->validateCompatibility($ingredientsInput, $typeProduit);
            if (!$compat['valid']) {
                return response()->json(['message' => 'Incompatibilité chimique détectée.', 'errors' => ['ingredients' => $compat['messages']]], 422);
            }

            $phEstime = $this->calculatorService->estimatePH($ingredientsInput, $typeProduit);
            $this->calculatorService->calculateGrams($ingredientsInput, $poidsLot);

            try {
                DB::transaction(function () use ($formule, $validated, $poidsLot, $notes, $typeProduit, $phEstime, $ingredientsInput) {
                    $formule->update([
                        'nom' => $validated['nom'] ?? $formule->nom,
                        'categorie' => $validated['categorie'] ?? $formule->categorie,
                        'type_produit' => $typeProduit,
                        'poids_lot' => $poidsLot,
                        'notes' => $notes,
                        'ph_estime' => $phEstime,
                    ]);

                    FormuleIngredient::where('formule_id', $formule->id)->delete();

                    foreach ($ingredientsInput as $ing) {
                        $ingredientObj = Ingredient::firstOrCreate(
                            ['nom' => $ing['nom_ingredient']],
                            [
                                'phase' => $ing['phase'],
                                'utilisateur_id' => Auth::id(),
                                'est_personnalise' => true
                            ]
                        );

                        FormuleIngredient::create([
                            'formule_id' => $formule->id,
                            'ingredient_id' => $ingredientObj->id,
                            'pourcentage' => $ing['pourcentage'],
                            'phase' => $ing['phase'],
                            'grammes_calculs' => $ing['grammes'],
                            'cout_par_kg' => $ing['cout_par_kg'],
                        ]);
                    }
                });
            } catch (\Exception $e) {
                return response()->json(['message' => 'Erreur lors de la mise à jour de la formule.', 'error' => $e->getMessage()], 500);
            }
        } else {
            // Mise à jour sans toucher aux ingrédients
            $formule->update([
                'nom' => $validated['nom'] ?? $formule->nom,
                'categorie' => $validated['categorie'] ?? $formule->categorie,
                'type_produit' => $typeProduit,
                'poids_lot' => $poidsLot,
                'notes' => $notes,
            ]);
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

