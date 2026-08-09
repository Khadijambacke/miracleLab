<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Paiement;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login')->withErrors(['email' => 'Vous devez être connecté pour payer.']);
        }

        // Simuler la validation du paiement
        $request->validate([
            'card_number' => 'required|string',
            'card_name' => 'required|string',
        ]);

        // Mettre à jour le statut
        $user->statut_abonnement = 'actif';
        $user->save();

        // Créer l'enregistrement
        Paiement::create([
            'utilisateur_id' => $user->id,
            'montant' => 15000,
            'statut' => 'REUSSI',
            'methode_paiement' => 'carte',
            'reference_transaction' => 'SIMUL_' . uniqid(),
            'date_paiement' => now(),
        ]);

        return redirect('/dashboard')->with('success', 'Paiement effectué avec succès ! Bienvenue dans votre laboratoire.');
    }
}
