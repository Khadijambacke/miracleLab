<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            if ($user->role === 'ADMIN' || $user->estAbonnementValide()) {
                return $next($request);
            }

            // Si l'abonnement a expiré, on met à jour le statut en base de données
            if (strtoupper($user->statut_abonnement ?? '') === 'ACTIF' && $user->date_expiration_abonnement && now()->greaterThan($user->date_expiration_abonnement)) {
                $user->statut_abonnement = 'INACTIF';
                $user->save();
            }
        }

        // Si c'est une requête API ou Ajax (Fetch), on renvoie une erreur JSON 403 Forbidden
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Votre abonnement est inactif. Veuillez procéder au paiement.'
            ], 403);
        }

        // Sinon (navigation normale) on renvoie vers le dashboard qui affichera le popup
        return redirect()->route('dashboard')->withErrors([
            'subscription' => 'Veuillez activer votre abonnement pour continuer.'
        ]);
    }
}
