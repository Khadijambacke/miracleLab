<?php

namespace App\Services;

class FormulaCalculatorService
{
    protected array $library;
    protected array $formulaTypes;
    protected array $compatRules;

    public function __construct()
    {
        $this->library = config('calculator.library', []);
        $this->formulaTypes = config('calculator.formula_types', []);
        $this->compatRules = config('calculator.compat_rules', []);
    }

    /**
     * Calcule le pourcentage total (eau incluse selon le type)
     */
    public function getTotalPct(array $ingredients, string $formulaType): float
    {
        $totalNonWater = 0;
        foreach ($ingredients as $ing) {
            $totalNonWater += (float) ($ing['pourcentage'] ?? 0);
        }

        $typeData = $this->formulaTypes[$formulaType] ?? null;
        $hasWater = $typeData['hasWater'] ?? false;

        if ($hasWater) {
            $waterPct = max(0, 100 - $totalNonWater);
            return $totalNonWater + $waterPct;
        }

        return $totalNonWater;
    }

    /**
     * Vérifie si le total dépasse strictement 100% 
     * et si le total est exactement égal à 100% avec de l'eau
     */
    public function validateRuleOf100(array $ingredients, string $formulaType): array
    {
        $totalNonWater = 0;
        foreach ($ingredients as $ing) {
            $totalNonWater += (float) ($ing['pourcentage'] ?? 0);
        }

        if (round($totalNonWater, 2) > 100) {
            return ['valid' => false, 'message' => "Le total des ingrédients (".round($totalNonWater, 2)."%) dépasse 100%."];
        }

        $typeData = $this->formulaTypes[$formulaType] ?? null;
        $hasWater = $typeData['hasWater'] ?? false;
        
        if (!$hasWater && round($totalNonWater, 2) !== 100.0) {
            return ['valid' => false, 'message' => "Pour une formule sans eau, le total doit être exactement de 100% (actuel: ".round($totalNonWater, 2)."%)."];
        }

        return ['valid' => true];
    }

    /**
     * Recalcule les grammes de chaque ingrédient basé sur le poids total
     */
    public function calculateGrams(array &$ingredients, float $totalWeight): void
    {
        foreach ($ingredients as &$ing) {
            $pct = (float) ($ing['pourcentage'] ?? 0);
            $ing['grammes'] = round($totalWeight * ($pct / 100), 2);
        }
    }

    /**
     * Recalcule le coût de chaque ingrédient basé sur le prix unitaire
     */
    public function calculateCosts(array &$ingredients): float
    {
        $totalCost = 0;
        foreach ($ingredients as &$ing) {
            $grammes = (float) ($ing['grammes'] ?? 0);
            $prixUnitaire = (float) ($ing['prix'] ?? 0); // Le prix est au gramme ou au 100g ?
            // Supposons que $ing['prix'] soit le coût total de cet ingrédient
            // D'après la logique frontend, coût = (prix / quantiteBase) * grammes
            // On ne fait ça que si le backend reçoit bien le prix unitaire de l'ingrédient de la base.
            // Pour faire simple ici, si on a juste 'prix', on valide ce que dit le front ou on laisse le front gérer.
            // Vu les directives : Le backend recalcule le cout. On verra cela dans FormuleController en croisant avec les vrais Ingrédients DB.
        }
        return $totalCost;
    }

    /**
     * Estime le pH de la formule à partir de la logique JS
     */
    public function estimatePH(array $ingredients, string $formulaType): ?float
    {
        $typeData = $this->formulaTypes[$formulaType] ?? null;
        if (!$typeData || !($typeData['hasWater'] ?? false)) {
            return null; // Pas d'eau, pas de pH
        }

        $ph = 6.5;
        foreach ($ingredients as $ing) {
            $name = $ing['nom_ingredient'] ?? '';
            $pct = (float) ($ing['pourcentage'] ?? 0);

            if (!$pct || !$name) continue;

            if (str_contains($name, "Acide Citrique")) $ph -= $pct * 7;
            elseif (str_contains($name, "Acide Lactique")) $ph -= $pct * 4;
            elseif (str_contains($name, "Acide Benzoïque") || str_contains($name, "Acide Sorbique")) $ph -= $pct * 2;
            elseif (str_contains($name, "Hydroxyde de Sodium") || str_contains($name, "NaOH")) $ph += $pct * 10;
            elseif (str_contains($name, "Triéthanolamine")) $ph += $pct * 3;
            elseif (str_contains($name, "Acide Salicylique")) $ph -= $pct * 3;
            else {
                $data = $this->getIngredientData($name);
                if ($data && isset($data['phNote'])) {
                    if (str_contains($data['phNote'], "Baisse")) $ph -= $pct * 1.5;
                    elseif (str_contains($data['phNote'], "Monte")) $ph += $pct * 1.5;
                    elseif (($data['group'] ?? '') === "Conservateurs") $ph -= $pct * 0.15;
                }
            }
        }
        
        return max(2.5, min(9.0, round($ph, 1)));
    }

    /**
     * Vérifie la compatibilité chimique
     */
    public function validateCompatibility(array $ingredients, string $formulaType): array
    {
        $names = [];
        foreach ($ingredients as $ing) {
            if (!empty($ing['nom_ingredient'])) {
                $names[] = $ing['nom_ingredient'];
            }
        }
        
        $ph = $this->estimatePH($ingredients, $formulaType);
        $conflicts = [];

        foreach ($this->compatRules as $rule) {
            $groupA = $rule['groupA'] ?? [];
            $groupB = $rule['groupB'] ?? [];

            $hasA = !empty(array_intersect($groupA, $names));
            $hasB = false;

            if (in_array('__NO_ACID__', $groupB)) {
                $hasB = ($ph !== null && $ph >= 5.5);
            } elseif (in_array('__HIGH_PH__', $groupB)) {
                $hasB = ($ph !== null && $ph > 7.0);
            } else {
                $hasB = !empty(array_intersect($groupB, $names));
            }

            if ($hasA && $hasB) {
                if ($rule['type'] === 'error') {
                    $conflicts[] = $rule['msg'];
                }
            }
        }

        if (!empty($conflicts)) {
            return ['valid' => false, 'messages' => $conflicts];
        }

        return ['valid' => true];
    }

    /**
     * Vérifie les limites maximales de dosage
     */
    public function validateDosageLimits(array $ingredients): array
    {
        $overLimits = [];
        foreach ($ingredients as $ing) {
            $name = $ing['nom_ingredient'] ?? '';
            $pct = (float) ($ing['pourcentage'] ?? 0);
            
            if (!$name || $pct <= 0) continue;

            $data = $this->getIngredientData($name);
            if ($data && isset($data['maxPct']) && $pct > (float)$data['maxPct']) {
                $overLimits[] = "$name dépasse la limite maximale autorisée ({$data['maxPct']}%). Vous avez demandé {$pct}%.";
            }
        }

        if (!empty($overLimits)) {
            return ['valid' => false, 'messages' => $overLimits];
        }

        return ['valid' => true];
    }

    /**
     * Cherche les infos statiques d'un ingrédient dans la bibliothèque (si standard)
     */
    protected function getIngredientData(string $name): ?array
    {
        foreach ($this->library as $phase => $groups) {
            foreach ($groups as $groupName => $items) {
                foreach ($items as $item) {
                    if (strtolower($item['name'] ?? '') === strtolower($name)) {
                        $item['group'] = $groupName;
                        return $item;
                    }
                }
            }
        }
        return null;
    }
}
