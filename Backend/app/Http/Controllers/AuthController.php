<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;

class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Traite l'inscription d'un nouvel utilisateur.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom_complet' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:utilisateurs'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Utilisateur::create([
            'nom_complet' => $validated['nom_complet'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? '',
            'mot_de_passe' => Hash::make($validated['password']),
            'role' => 'CLIENT',
            'statut_abonnement' => 'ACTIF',
            'type_plan' => 'ESSAI_GRATUIT',
            'date_expiration_abonnement' => now()->addDays(3),
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    /**
     * Traite la soumission du formulaire de connexion.
     */
    public function login(Request $request)
    {
        // Validation basique
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'], // L'input HTML sera 'password', Laravel le mappera sur 'mot_de_passe' via Utilisateur::getAuthPassword()
        ]);

        // Tentative de connexion
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirection vers le dashboard principal
            return redirect()->intended('dashboard');
        }

        // En cas d'échec
        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
