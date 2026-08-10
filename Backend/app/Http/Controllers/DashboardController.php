<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Formule;
use App\Models\Ingredient;
use App\Models\Utilisateur;
use App\Models\Message;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord approprié selon le rôle de l'utilisateur.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Si l'utilisateur est ADMIN et ne demande pas explicitement la vue client (?view=client)
        if ($user->role === 'ADMIN' && $request->query('view') !== 'client') {
            $clients = Utilisateur::where('role', 'CLIENT')->withCount('formules')->get();
            $totalUsers = $clients->count();
            $totalFormules = Formule::count();
            $totalIngredients = Ingredient::count();

            // Charger tous les ingrédients pour la gestion admin
            $ingredients = Ingredient::orderBy('nom', 'asc')->get();

            // Charger les messages du chat support groupés par email de client
            $rawMessages = Message::with(['expediteur', 'destinataire'])->orderBy('created_at', 'asc')->get();
            $chats = [];
            foreach ($rawMessages as $msg) {
                $clientEmail = $msg->expediteur && $msg->expediteur->role === 'CLIENT' 
                    ? $msg->expediteur->email 
                    : ($msg->destinataire ? $msg->destinataire->email : 'client@missmiracle.com');
                
                if (!isset($chats[$clientEmail])) {
                    $chats[$clientEmail] = [];
                }

                $chats[$clientEmail][] = [
                    'sender' => ($msg->expediteur && $msg->expediteur->role === 'ADMIN') ? 'support' : 'client',
                    'text' => $msg->message,
                    'time' => $msg->created_at ? $msg->created_at->format('H:i') : '12:00'
                ];
            }

            return view('dashboard-admin', compact('clients', 'totalUsers', 'totalFormules', 'totalIngredients', 'ingredients', 'chats', 'user'));
        }

        // Vérification d'accès strict : Si inactif, on ne charge AUCUNE donnée du dashboard. On retourne juste la vue de paiement bloquante.
        if ($user->role !== 'ADMIN' && strtoupper($user->statut_abonnement ?? '') !== 'ACTIF') {
            return view('payment', ['needsPayment' => true]);
        }

        // Pour le client (ou l'admin en mode aperçu client) : Ses propres formules
        $formules = Formule::where('utilisateur_id', $user->id)
                           ->with('formuleIngredients.ingredient')
                           ->orderBy('created_at', 'desc')
                           ->get();
        
        // Ingrédients : Globaux (utilisateur_id = null) + les siens
        $ingredients = Ingredient::whereNull('utilisateur_id')
                                 ->orWhere('utilisateur_id', $user->id)
                                 ->orderBy('nom', 'asc')
                                 ->get();

        // Messages du chat pour l'utilisateur
        $rawMessages = Message::where('expediteur_id', $user->id)
                               ->orWhere('destinataire_id', $user->id)
                               ->orderBy('created_at', 'asc')
                               ->get();
        
        $chats = [
            $user->email => $rawMessages->map(function ($msg) use ($user) {
                return [
                    'sender' => $msg->expediteur_id === $user->id ? 'client' : 'support',
                    'text' => $msg->message,
                    'time' => $msg->created_at ? $msg->created_at->format('H:i') : '12:00'
                ];
            })->toArray()
        ];

        return view('dashboard-client', compact('formules', 'ingredients', 'chats', 'user'));
    }
}

