<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\CategorieIngredient;
use App\Models\Ingredient;
use App\Models\FicheTechnique;

class ImportIngredientsSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('data/ingredients.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error("Le fichier $jsonPath n'existe pas.");
            return;
        }

        $data = json_decode(File::get($jsonPath), true);
        
        if (!$data) {
            $this->command->error("Erreur de décodage JSON.");
            return;
        }

        $library = $data['library'] ?? [];
        $sheets = $data['sheets'] ?? [];

        // Ne pas supprimer les données existantes si on veut garder le DemoSeeder,
        // ou bien vider les tables avant. Pour l'instant, on ajoute juste ce qui manque.
        // On va faire un updateOrCreate basé sur le nom de l'ingrédient.

        foreach ($library as $phase => $categories) {
            foreach ($categories as $catName => $ingredients) {
                
                // Créer ou récupérer la catégorie
                $categorie = CategorieIngredient::firstOrCreate(
                    ['nom' => $catName],
                    ['description' => "Catégorie importée: $catName"]
                );

                foreach ($ingredients as $ingData) {
                    $ingName = $ingData['name'] ?? null;
                    if (!$ingName) continue;

                    // Créer l'ingrédient
                    $ingredient = Ingredient::updateOrCreate(
                        ['nom' => $ingName],
                        [
                            'categorie_id' => $categorie->id,
                            'phase' => $phase,
                            'nom_groupe' => $catName,
                            'note' => $ingData['note'] ?? null,
                            'pourcentage_min' => $ingData['minPct'] ?? null,
                            'pourcentage_max' => $ingData['maxPct'] ?? null,
                            'impact_ph' => $ingData['impactPh'] ?? 0.0,
                            'est_personnalise' => false,
                        ]
                    );

                    // Associer la fiche technique si elle existe dans 'sheets'
                    if (isset($sheets[$ingName])) {
                        $sheetData = $sheets[$ingName];
                        
                        FicheTechnique::updateOrCreate(
                            ['ingredient_id' => $ingredient->id],
                            [
                                'nom_inci' => $sheetData['INCI'] ?? null,
                                'categorie_fonctionnelle' => $sheetData['Fonction'] ?? null,
                                'solubilite' => $sheetData['Solubilité'] ?? null,
                                'temperature_incorporation' => $sheetData['Chauffe'] ?? null,
                                'ph_optimal_min' => isset($sheetData['pH Optimal']) ? floatval(explode('-', $sheetData['pH Optimal'])[0]) : null,
                                'precautions' => $sheetData['Précautions'] ?? null,
                                'conseils_formulateur' => $sheetData['Conseils'] ?? null,
                            ]
                        );
                    }
                }
            }
        }

        $this->command->info("L'importation de " . count($library) . " phases est terminée.");
    }
}
