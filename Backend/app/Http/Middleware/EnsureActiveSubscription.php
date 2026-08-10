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

        // Les ADMINS bypassent le blocage
        // Les clients avec statut ACTIF passent
        if ($user && ($user->role === 'ADMIN' || strtoupper($user->statut_abonnement ?? '') === 'ACTIF')) {
            return $next($request);
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
