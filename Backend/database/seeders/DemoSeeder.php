<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use App\Models\CategorieIngredient;
use App\Models\Ingredient;
use App\Models\FicheTechnique;
use App\Models\ProprieteIngredient;
use App\Models\TypeProduit;
use App\Models\CiblePh;
use App\Models\RegleCompatibilite;
use App\Models\Message;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création des Utilisateurs Démo (Admin & Clients)
        $admin = Utilisateur::create([
            'nom_complet' => 'Astou Ndiaye (MM)',
            'email' => 'admin@example.com',
            'telephone' => '+221 77 000 00 00',
            'mot_de_passe' => Hash::make('admin123'),
            'role' => 'ADMIN',
            'statut_abonnement' => 'ACTIF',
        ]);

        $clientAwa = Utilisateur::create([
            'nom_complet' => 'Awa Diop',
            'email' => 'awa.diop@example.com',
            'telephone' => '+221 77 123 45 67',
            'mot_de_passe' => Hash::make('client123'),
            'role' => 'CLIENT',
            'statut_abonnement' => 'ACTIF',
        ]);

        Utilisateur::create([
            'nom_complet' => 'Babacar Sy',
            'email' => 'babacar.sy@example.com',
            'telephone' => '+221 78 987 65 43',
            'mot_de_passe' => Hash::make('client123'),
            'role' => 'CLIENT',
            'statut_abonnement' => 'ACTIF',
        ]);

        Utilisateur::create([
            'nom_complet' => 'Moussa Ndiaye',
            'email' => 'moussa.n@example.com',
            'telephone' => '+221 70 111 22 33',
            'mot_de_passe' => Hash::make('client123'),
            'role' => 'CLIENT',
            'statut_abonnement' => 'INACTIF',
        ]);

        // 2. Création des Catégories d'Ingrédients
        $catTensioactifs = CategorieIngredient::create(['nom' => 'Tensioactifs', 'description' => 'Agents moussants et nettoyants']);
        $catHumectants = CategorieIngredient::create(['nom' => 'Humectants', 'description' => 'Attirent et retiennent l\'eau']);
        $catEmulsifiants = CategorieIngredient::create(['nom' => 'Émulsifiants', 'description' => 'Lient l\'eau et l\'huile']);
        $catActifs = CategorieIngredient::create(['nom' => 'Actifs Capillaires & Vitamines', 'description' => 'Soins ciblés']);
        $catBasesAqueuses = CategorieIngredient::create(['nom' => 'Bases Aqueuses', 'description' => 'Support d\'hydratation']);

        // 3. Création des Ingrédients de Base et Fiches Techniques
        $eau = Ingredient::create([
            'categorie_id' => $catBasesAqueuses->id,
            'nom' => 'Eau Distillée',
            'phase' => 'AQUEUSE',
            'nom_groupe' => 'Bases Aqueuses',
            'note' => 'Base aqueuse principale, neutre',
            'est_personnalise' => false,
        ]);
        FicheTechnique::create([
            'ingredient_id' => $eau->id,
            'nom_inci' => 'Aqua',
            'categorie_fonctionnelle' => 'Solvant',
            'solubilite' => 'Hydrosoluble',
            'temperature_incorporation' => 'Toutes températures',
            'ph_optimal_min' => 5.0,
            'ph_optimal_max' => 7.0,
            'precautions' => 'Utiliser de l\'eau stérile.',
            'conseils_formulateur' => 'Base indispensable pour les émulsions.',
        ]);

        $aloe = Ingredient::create([
            'categorie_id' => $catBasesAqueuses->id,
            'nom' => 'Jus d\'Aloe Vera',
            'phase' => 'AQUEUSE',
            'nom_groupe' => 'Bases Aqueuses',
            'note' => 'Hydratant, apaisant, démêlant naturel',
            'pourcentage_max' => 30.00,
            'impact_ph' => -0.5,
            'est_personnalise' => false,
        ]);
        FicheTechnique::create([
            'ingredient_id' => $aloe->id,
            'nom_inci' => 'Aloe Barbadensis Leaf Juice',
            'categorie_fonctionnelle' => 'Actif hydratant / Base',
            'solubilite' => 'Hydrosoluble',
            'temperature_incorporation' => '< 40°C',
            'ph_optimal_min' => 3.5,
            'ph_optimal_max' => 5.0,
            'precautions' => 'Sensible à la forte chaleur.',
            'conseils_formulateur' => 'Idéal dans les sprays et lotions.',
        ]);
        ProprieteIngredient::create(['ingredient_id' => $aloe->id, 'libelle' => 'Hydratation profonde']);
        ProprieteIngredient::create(['ingredient_id' => $aloe->id, 'libelle' => 'Apaisant cutané']);

        $btms = Ingredient::create([
            'categorie_id' => $catEmulsifiants->id,
            'nom' => 'BTMS-50',
            'phase' => 'HUILEUSE',
            'nom_groupe' => 'Émulsifiants',
            'note' => 'Émulsifiant cationique, démêlant, conditionneur',
            'pourcentage_min' => 2.00,
            'pourcentage_max' => 8.00,
            'est_personnalise' => false,
        ]);
        FicheTechnique::create([
            'ingredient_id' => $btms->id,
            'nom_inci' => 'Behentrimonium Methosulfate (and) Cetyl Alcohol',
            'categorie_fonctionnelle' => 'Émulsifiant cationique',
            'solubilite' => 'Liposoluble',
            'temperature_incorporation' => 'Fondre à 75°C',
            'ph_optimal_min' => 4.0,
            'ph_optimal_max' => 5.5,
            'precautions' => 'Charge positive (+) — Incompatible avec tensioactifs anioniques (-).',
            'conseils_formulateur' => 'Émulsifiant idéal pour masques et après-shampoings.',
        ]);
        ProprieteIngredient::create(['ingredient_id' => $btms->id, 'libelle' => 'Démêlant puissant']);

        $panthenol = Ingredient::create([
            'categorie_id' => $catActifs->id,
            'nom' => 'Panthénol (B5)',
            'phase' => 'REFROIDISSEMENT',
            'nom_groupe' => 'Actifs Capillaires',
            'note' => 'Hydratant, renforce et répare la fibre',
            'pourcentage_max' => 5.00,
            'est_personnalise' => false,
        ]);
        FicheTechnique::create([
            'ingredient_id' => $panthenol->id,
            'nom_inci' => 'Panthenol',
            'categorie_fonctionnelle' => 'Vitamine / Hydratant',
            'solubilite' => 'Hydrosoluble',
            'temperature_incorporation' => '< 40°C',
            'ph_optimal_min' => 4.0,
            'ph_optimal_max' => 6.0,
            'precautions' => 'Visqueux, incorporer en fin de formule.',
            'conseils_formulateur' => 'Préserve l\'élasticité du cheveu.',
        ]);

        // 4. Création des Types de Produits (Squelettes)
        $leaveIn = TypeProduit::create([
            'nom' => 'Leave-in Spray',
            'code' => 'LEAVE_IN',
            'categorie' => 'haircare',
            'squelette' => [
                ['name' => "Jus d'Aloe Vera", 'pct' => 20, 'phase' => 'AQUEUSE'],
                ['name' => "Glycérine Végétale", 'pct' => 3, 'phase' => 'AQUEUSE'],
                ['name' => "Panthénol (B5)", 'pct' => 2, 'phase' => 'REFROIDISSEMENT'],
            ]
        ]);
        CiblePh::create(['type_produit_id' => $leaveIn->id, 'ph_min' => 4.5, 'ph_max' => 5.5]);

        $shampoing = TypeProduit::create([
            'nom' => 'Shampoing Doux',
            'code' => 'SHAMPOING',
            'categorie' => 'haircare',
            'squelette' => [
                ['name' => "Eau Distillée", 'pct' => 54, 'phase' => 'AQUEUSE'],
                ['name' => "Glycérine Végétale", 'pct' => 3, 'phase' => 'AQUEUSE'],
                ['name' => "Panthénol (B5)", 'pct' => 2, 'phase' => 'REFROIDISSEMENT'],
            ]
        ]);
        CiblePh::create(['type_produit_id' => $shampoing->id, 'ph_min' => 5.0, 'ph_max' => 6.5]);

        // 5. Règles de Compatibilité Chimique
        RegleCompatibilite::create([
            'nom_regle' => 'Conflit Cationique / Anionique',
            'groupe_a' => ["BTMS-50", "BTMS-25", "Behentrimonium Chloride", "Honeyquat", "Polyquaternium-7"],
            'groupe_b' => ["Texapon N70", "Texapon NSO", "Coco Glucoside", "Decyl Glucoside", "SCI"],
            'niveau' => 'error',
            'message_alerte' => 'Ingrédient cationique (+) et anionique (-) ensemble -> risque de floculation et déstabilisation de l\'émulsion.'
        ]);

        RegleCompatibilite::create([
            'nom_regle' => 'Inactivation Conservateur Benzoate/Sorbate',
            'groupe_a' => ["Benzoate de Sodium", "Sorbate de Potassium", "Acide Benzoïque"],
            'groupe_b' => ["__NO_ACID__"],
            'niveau' => 'warn',
            'message_alerte' => 'Benzoate/Sorbate inactifs sans acidifiant -> Ajouter Acide Citrique ou Lactique pour descendre le pH sous 5.5.'
        ]);

        // 6. Messages de Chat Support Initial
        Message::create([
            'expediteur_id' => $clientAwa->id,
            'destinataire_id' => $admin->id,
            'message' => 'Bonjour, j\'ai une question sur l\'émulsifiant BTMS-50.',
            'est_lu' => true,
        ]);
        Message::create([
            'expediteur_id' => $admin->id,
            'destinataire_id' => $clientAwa->id,
            'message' => 'Bonjour Awa ! Oui, je t\'écoute. Qu\'aimerais-tu savoir ?',
            'est_lu' => true,
        ]);
    }
}
