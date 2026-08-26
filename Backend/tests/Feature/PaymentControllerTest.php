<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Utilisateur;
use App\Models\Paiement;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_success_route_returns_close_popup_view()
    {
        $user = Utilisateur::factory()->create(['statut_abonnement' => 'INACTIF']);
        $paiement = Paiement::create([
            'utilisateur_id' => $user->id,
            'montant' => 2900,
            'type_plan' => 'MENSUEL',
            'statut' => 'EN_ATTENTE',
            'methode_paiement' => 'PAYTECH',
            'reference_transaction' => 'TEST_REF',
            'date_paiement' => now(),
        ]);

        $response = $this->actingAs($user)->get("/payment/success?ref=TEST_REF");

        $response->assertStatus(200);

        $user->refresh();
        $paiement->refresh();

        // The user subscription should still be INACTIF because ONLY the IPN webhook can activate it
        $this->assertEquals('INACTIF', $user->statut_abonnement);
        $this->assertEquals('EN_ATTENTE', $paiement->statut);
    }

    public function test_paytech_ipn_activates_subscription_for_1_month()
    {
        $user = Utilisateur::factory()->create(['statut_abonnement' => 'INACTIF']);
        $paiement = Paiement::create([
            'utilisateur_id' => $user->id,
            'montant' => 2900,
            'type_plan' => 'MENSUEL',
            'statut' => 'EN_ATTENTE',
            'methode_paiement' => 'PAYTECH',
            'reference_transaction' => 'TEST_REF_IPN_1M',
            'date_paiement' => now(),
        ]);

        $response = $this->postJson("/paytech/ipn", [
            'type_event' => 'sale_complete',
            'ref_command' => 'TEST_REF_IPN_1M',
            'item_price' => 2900,
        ]);

        $response->assertStatus(200);

        $user->refresh();
        $paiement->refresh();

        $this->assertEquals('ACTIF', $user->statut_abonnement);
        $this->assertEquals('MENSUEL', $user->type_plan);
        $this->assertNotNull($user->date_expiration_abonnement);
        $this->assertEquals('REUSSI', $paiement->statut);
    }

    public function test_paytech_ipn_activates_subscription_for_3_months()
    {
        $user = Utilisateur::factory()->create(['statut_abonnement' => 'INACTIF']);
        $paiement = Paiement::create([
            'utilisateur_id' => $user->id,
            'montant' => 5900,
            'type_plan' => 'TRIMESTRIEL',
            'statut' => 'EN_ATTENTE',
            'methode_paiement' => 'PAYTECH',
            'reference_transaction' => 'TEST_REF_IPN_3M',
            'date_paiement' => now(),
        ]);

        $response = $this->postJson("/paytech/ipn", [
            'type_event' => 'sale_complete',
            'ref_command' => 'TEST_REF_IPN_3M',
            'item_price' => 5900,
        ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertEquals('ACTIF', $user->statut_abonnement);
        $this->assertEquals('TRIMESTRIEL', $user->type_plan);
    }

    public function test_paytech_process_creates_payment_record_with_selected_plan()
    {
        $user = Utilisateur::factory()->create(['statut_abonnement' => 'INACTIF']);

        $response = $this->actingAs($user)->postJson('/paytech/process', [
            'plan' => '12_mois',
        ]);

        $response->assertStatus(200);

        $paiement = Paiement::where('utilisateur_id', $user->id)->first();
        $this->assertNotNull($paiement);
        $this->assertEquals('ANNUEL', $paiement->type_plan);
        $this->assertEquals(17900, $paiement->montant);
    }
}
