<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Utilisateur;
use App\Models\Formule;

class FormuleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_update_another_users_formula()
    {
        $user1 = Utilisateur::factory()->create(['statut_abonnement' => 'ACTIF']);
        $user2 = Utilisateur::factory()->create(['statut_abonnement' => 'ACTIF']);

        $formule = Formule::create([
            'utilisateur_id' => $user1->id,
            'nom' => 'Shampooing Doux',
            'categorie' => 'haircare',
            'type_produit' => 'SHAMPOO',
            'poids_lot' => 1000,
        ]);

        $response = $this->actingAs($user2)->putJson("/formules/{$formule->id}", [
            'nom' => 'Hacked Name'
        ]);
        
        $response->dump();

        $response->assertStatus(403);
        $this->assertDatabaseHas('formules', [
            'id' => $formule->id,
            'nom' => 'Shampooing Doux'
        ]);
    }

    public function test_user_cannot_delete_another_users_formula()
    {
        $user1 = Utilisateur::factory()->create(['statut_abonnement' => 'ACTIF']);
        $user2 = Utilisateur::factory()->create(['statut_abonnement' => 'ACTIF']);

        $formule = Formule::create([
            'utilisateur_id' => $user1->id,
            'nom' => 'Shampooing Doux',
            'categorie' => 'haircare',
            'type_produit' => 'SHAMPOO',
            'poids_lot' => 1000,
        ]);

        $response = $this->actingAs($user2)->deleteJson("/formules/{$formule->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('formules', [
            'id' => $formule->id,
        ]);
    }

    public function test_formula_must_sum_to_100_percent()
    {
        $user = Utilisateur::factory()->create(['statut_abonnement' => 'ACTIF']);

        // Une formule sans eau (BAUME_CORPS par exemple) doit faire exactement 100%
        $response = $this->actingAs($user)->postJson("/formules", [
            'nom' => 'Baume 120%',
            'type_produit' => 'BAUME_CORPS',
            'ingredients' => [
                ['nom' => 'Beurre de Karité', 'pourcentage' => 80],
                ['nom' => 'Huile de Coco', 'pourcentage' => 40],
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Le total des ingrédients (120%) dépasse 100%.']);
    }
}
