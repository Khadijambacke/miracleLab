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
            $totalUsers    = $clients->count();
            $totalFormules = Formule::count();
            $totalIngredients = Ingredient::count();

            // ── Real Stats ───────────────────────────────────────────────────
            $activeUsers  = $clients->filter(fn($c) => strtoupper($c->statut_abonnement ?? '') === 'ACTIF')->count();
            $inactiveUsers = $totalUsers - $activeUsers;
            $realRevenue  = (float) \App\Models\Paiement::where('statut', 'REUSSI')->sum('montant');
            $conversionRate = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0;

            // Formules par catégorie
            $formulasByCategory = Formule::selectRaw("categorie, COUNT(*) as total")
                ->groupBy('categorie')
                ->pluck('total', 'categorie')
                ->toArray();
            $skincareCount = $formulasByCategory['skincare'] ?? ($formulasByCategory['Skincare'] ?? 0);
            $haircareCount = $formulasByCategory['haircare'] ?? ($formulasByCategory['Haircare'] ?? 0);
            $autresCount   = $totalFormules - $skincareCount - $haircareCount;

            // Top ingrédients les plus utilisés (via formule_ingredients)
            $topIngredients = \DB::table('formule_ingredients')
                ->join('ingredients', 'formule_ingredients.ingredient_id', '=', 'ingredients.id')
                ->selectRaw('ingredients.nom, COUNT(*) as usage_count')
                ->whereNotNull('ingredients.id')
                ->groupBy('ingredients.id', 'ingredients.nom')
                ->orderByDesc('usage_count')
                ->limit(5)
                ->get()
                ->toArray();

            // Répartition ingrédients par phase
            $ingByPhase = Ingredient::selectRaw("UPPER(phase) as phase, COUNT(*) as total")
                ->groupBy('phase')
                ->pluck('total', 'phase')
                ->toArray();
            $ingAqueuse       = $ingByPhase['AQUEUSE'] ?? 0;
            $ingHuileuse      = $ingByPhase['HUILEUSE'] ?? 0;
            $ingRefroidissement = $ingByPhase['REFROIDISSEMENT'] ?? 0;
            $ingTotal         = max($ingAqueuse + $ingHuileuse + $ingRefroidissement, 1);

            $stats = [
                'totalUsers'          => $totalUsers,
                'activeUsers'         => $activeUsers,
                'inactiveUsers'       => $inactiveUsers,
                'totalFormules'       => $totalFormules,
                'totalIngredients'    => $totalIngredients,
                'realRevenue'         => $realRevenue,
                'conversionRate'      => $conversionRate,
                'skincareCount'       => $skincareCount,
                'haircareCount'       => $haircareCount,
                'autresCount'         => max($autresCount, 0),
                'topIngredients'      => $topIngredients,
                'ingAqueuse'          => $ingAqueuse,
                'ingHuileuse'         => $ingHuileuse,
                'ingRefroidissement'  => $ingRefroidissement,
                'ingAqueusePct'       => round(($ingAqueuse / $ingTotal) * 100),
                'ingHuileusePct'      => round(($ingHuileuse / $ingTotal) * 100),
                'ingRefroidissementPct' => round(($ingRefroidissement / $ingTotal) * 100),
            ];

            // Charger tous les ingrédients pour la gestion admin
            $ingredients = Ingredient::with('ficheTechnique')->orderBy('nom', 'asc')->get();

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

            return view('dashboard-admin', compact('clients', 'totalUsers', 'totalFormules', 'totalIngredients', 'ingredients', 'chats', 'user', 'stats'));
        }

        // Vérification d'accès : Si l'abonnement/essai gratuit n'est plus valide, on affiche la vue de paiement bloquante.
        if ($user->role !== 'ADMIN' && !$user->estAbonnementValide()) {
            if (strtoupper($user->statut_abonnement ?? '') === 'ACTIF' && $user->date_expiration_abonnement && now()->greaterThan($user->date_expiration_abonnement)) {
                $user->statut_abonnement = 'INACTIF';
                $user->save();
            }
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
                                 ->with('ficheTechnique')
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

