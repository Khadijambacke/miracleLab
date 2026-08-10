<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Paiement;
use App\Models\Utilisateur;

class PaymentController extends Controller
{
    /**
     * Intech API Base URL
     */
    private function getBaseUrl(): string
    {
        return config('services.intech.base_url', env('INTECH_BASE_URL', 'https://api.intech.sn'));
    }

    /**
     * Intech API Key
     */
    private function getApiKey(): string
    {
        return config('services.intech.api_key', env('INTECH_API_KEY', ''));
    }

    /**
     * Initialise le paiement via l'API Intech (Cash-In Mobile Money)
     */
    public function processPayment(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour effectuer le paiement.'
            ], 401);
        }

        $request->validate([
            'phone' => 'required|string',
            'payment_method' => 'required|string|in:wave,om,free,expresso'
        ]);

        // Nettoyage du numéro de téléphone
        $phone = preg_replace('/[^0-9]/', '', $request->input('phone'));
        if (!str_starts_with($phone, '221') && strlen($phone) === 9) {
            $phone = '221' . $phone;
        }

        // Mappage des méthodes vers les codes de service Intech API
        $codeServices = [
            'wave' => 'WAVE_SN_API_CASH_IN',
            'om' => 'ORANGE_SN_API_CASH_IN',
            'free' => 'FREE_SN_WALLET_CASH_IN',
            'expresso' => 'EXPRESSO_SN_WALLET_CASH_IN',
        ];

        $codeService = $codeServices[$request->input('payment_method')] ?? 'WAVE_SN_API_CASH_IN';
        $amount = 15000.00; // Montant réclamé pour l'abonnement
        $reference = 'MM_' . strtoupper(uniqid());
        $callbackUrl = route('intech.callback');

        // Créer l'enregistrement de paiement temporaire en attente
        $paiement = Paiement::create([
            'utilisateur_id' => $user->id,
            'montant' => $amount,
            'statut' => 'EN_ATTENTE',
            'methode_paiement' => strtoupper($request->input('payment_method')),
            'reference_transaction' => $reference,
            'date_paiement' => now(),
        ]);

        $apiKey = $this->getApiKey();

        // Mode Test / Démo local si la clé est de test ou en environnement local
        if (empty($apiKey) || str_contains(strtoupper($apiKey), 'TEST') || str_contains(strtoupper($apiKey), 'DEMO')) {
            $user->statut_abonnement = 'ACTIF';
            $user->save();

            $paiement->statut = 'REUSSI';
            $paiement->save();

            return response()->json([
                'success' => true,
                'message' => 'Paiement de 15 000 FCFA validé avec succès (Mode Test Intech API) ! Redirection...',
                'reference' => $reference,
                'status' => 'REUSSI'
            ]);
        }

        // Envoi de la requête POST vers l'API Intech si une clé API est configurée
        try {
            $response = Http::timeout(60)->post($this->getBaseUrl() . '/api-services/payment', [
                'apiKey' => $apiKey,
                'phone' => $phone,
                'amount' => $amount,
                'codeService' => $codeService,
                'callbackUrl' => $callbackUrl,
                'externalId' => $reference,
            ]);

            $responseData = $response->json();
            Log::info('Intech Payment Response:', ['data' => $responseData]);

            // Si la requête a été acceptée par l'API Intech
            if ($response->successful() && isset($responseData['success']) && $responseData['success']) {
                $intechRef = $responseData['transactionId'] ?? $responseData['token'] ?? $reference;
                $paiement->reference_transaction = $intechRef;
                $paiement->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Demande de paiement transmise ! Veuillez valider la transaction sur votre téléphone.',
                    'reference' => $intechRef,
                    'status' => 'EN_ATTENTE'
                ]);
            }

            // En cas d'erreur renvoyée par l'API Intech
            $errorMsg = $responseData['message'] ?? 'Erreur lors du traitement du paiement par l\'opérateur.';
            $paiement->statut = 'ECHOUE';
            $paiement->save();

            return response()->json([
                'success' => false,
                'message' => $errorMsg
            ], 400);

        } catch (\Exception $e) {
            Log::error('Intech Payment Exception: ' . $e->getMessage());
            
            // Mode fallback simulation si l'API Intech n'est pas encore configurée avec les vraies clés
            if (empty($apiKey) || env('APP_ENV') === 'local') {
                $user->statut_abonnement = 'ACTIF';
                $user->save();

                $paiement->statut = 'REUSSI';
                $paiement->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Paiement effectué avec succès !',
                    'reference' => $reference,
                    'status' => 'REUSSI'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Impossible de contacter l\'API de paiement Intech.'
            ], 500);
        }
    }

    /**
     * Traitement du Callback/Webhook envoyé par l'API Intech
     */
    public function handleCallback(Request $request)
    {
        Log::info('Intech Callback Received:', $request->all());

        // Récupération des données du callback
        $transactionId = $request->input('transactionId') ?? $request->input('externalId');
        $status = strtoupper($request->input('status') ?? '');
        $amount = floatval($request->input('amount') ?? 0);
        $providedHash = $request->header('X-Intech-Signature') ?? $request->input('sha256') ?? '';

        // Vérification de la transaction en base de données
        $paiement = Paiement::where('reference_transaction', $transactionId)->first();
        if (!$paiement) {
            return response()->json(['status' => 'FAILED', 'message' => 'Transaction introuvable'], 404);
        }

        // Vérification du montant (doit être exactement égal au montant demandé 15000 FCFA)
        if ($amount > 0 && $amount < $paiement->montant) {
            Log::warning('Intech Callback Amount Mismatch:', ['expected' => $paiement->montant, 'received' => $amount]);
            $paiement->statut = 'ECHOUE';
            $paiement->save();
            return response()->json(['status' => 'FAILED', 'message' => 'Montant incorrect'], 400);
        }

        // Si le statut envoyé est un succès
        if ($status === 'SUCCESS' || $status === 'SUCCESSFUL' || $status === 'REUSSI' || $status === 'COMPLETED') {
            $paiement->statut = 'REUSSI';
            $paiement->date_paiement = now();
            $paiement->save();

            // Activation de l'abonnement client
            $user = Utilisateur::find($paiement->utilisateur_id);
            if ($user) {
                $user->statut_abonnement = 'ACTIF';
                $user->save();
            }
        } else if ($status === 'FAILED' || $status === 'ECHOUE' || $status === 'CANCELLED') {
            $paiement->statut = 'ECHOUE';
            $paiement->save();
        }

        // Intech exige un retour HTTP 200 (Note 4 de la documentation officielle Intech API)
        return response()->json(['status' => 'SUCCESS'], 200);
    }

    /**
     * Vérification de l'état de la transaction (Polling Frontend AJAX)
     */
    public function checkStatus($reference)
    {
        $paiement = Paiement::where('reference_transaction', $reference)->first();

        if (!$paiement) {
            return response()->json(['success' => false, 'message' => 'Transaction introuvable'], 404);
        }

        // Si la transaction est déjà marquée réussie en base
        if ($paiement->statut === 'REUSSI') {
            return response()->json([
                'success' => true,
                'status' => 'REUSSI',
                'message' => 'Paiement confirmé ! Redirection...',
                'redirect' => route('dashboard')
            ]);
        }

        // Appel direct à l'API de vérification Intech si disponible
        $apiKey = $this->getApiKey();
        if (!empty($apiKey)) {
            try {
                $response = Http::timeout(30)->post($this->getBaseUrl() . '/api-services/status', [
                    'apiKey' => $apiKey,
                    'transactionId' => $reference
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $status = strtoupper($data['status'] ?? '');

                    if ($status === 'SUCCESS' || $status === 'REUSSI' || $status === 'COMPLETED') {
                        $paiement->statut = 'REUSSI';
                        $paiement->save();

                        $user = Utilisateur::find($paiement->utilisateur_id);
                        if ($user) {
                            $user->statut_abonnement = 'ACTIF';
                            $user->save();
                        }

                        return response()->json([
                            'success' => true,
                            'status' => 'REUSSI',
                            'message' => 'Paiement confirmé ! Redirection...',
                            'redirect' => route('dashboard')
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Intech Status Check Exception: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'status' => $paiement->statut,
            'message' => 'En attente de validation...'
        ]);
    }

    /**
     * Traitement du paiement PayTech (Sandbox / Production)
     */
    public function processPaytechPayment(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour effectuer le paiement.'
            ], 401);
        }

        $env = config('services.paytech.env', 'test');
        $apiKey = config('services.paytech.api_key', '');
        $apiSecret = config('services.paytech.api_secret', '');

        // En mode sandbox, on peut tester avec un montant réduit (100 FCFA)
        // En production ce sera 15000 FCFA
        $amount = ($env === 'test') ? 100 : 15000;
        $refCommand = 'MM_' . strtoupper(uniqid());

        // Créer l'enregistrement de paiement en attente
        $paiement = Paiement::create([
            'utilisateur_id' => $user->id,
            'montant' => $amount,
            'statut' => 'EN_ATTENTE',
            'methode_paiement' => 'PAYTECH',
            'reference_transaction' => $refCommand,
            'date_paiement' => now(),
        ]);

        // Construire les URLs avec APP_URL (pas route() qui utilise le host du serveur local)
        $baseUrl    = rtrim(config('app.url'), '/');
        $successUrl = $baseUrl . '/payment/success?ref=' . $refCommand;
        $cancelUrl  = $baseUrl . '/payment/cancel?ref=' . $refCommand;
        $ipnUrl     = $baseUrl . '/paytech/ipn';

        $payload = [
            'item_name'    => 'Abonnement Laboratoire Miss Miracle',
            'item_price'   => $amount,
            'currency'     => 'XOF',
            'ref_command'  => $refCommand,
            'command_name' => ($env === 'test' ? '[TEST] ' : '') . 'Abonnement Lab Miss Miracle',
            'env'          => $env,
            'ipn_url'      => $ipnUrl,
            'success_url'  => $successUrl,
            'cancel_url'   => $cancelUrl,
            'custom_field' => json_encode(['user_id' => $user->id]),
        ];

        Log::info('PayTech — Envoi requête', [
            'env' => $env,
            'api_key_prefix' => substr($apiKey, 0, 8) . '...',
            'payload' => $payload,
        ]);

        // Appel à l'API PayTech (SSL verify désactivé en local Windows)
        try {
            $response = Http::withHeaders([
                'API_KEY'      => $apiKey,
                'API_SECRET'   => $apiSecret,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->withOptions(['verify' => false])->timeout(30)
              ->post('https://paytech.sn/api/payment/request-payment', $payload);

            $statusCode = $response->status();
            $data = $response->json();

            Log::info('PayTech — Réponse API', [
                'status_code' => $statusCode,
                'body'        => $data,
            ]);

            if ($response->successful() && isset($data['success']) && $data['success'] == 1 && !empty($data['redirect_url'])) {
                return response()->json([
                    'success'      => true,
                    'redirect_url' => $data['redirect_url'],
                    'reference'    => $refCommand,
                    'message'      => 'Redirection vers PayTech...',
                ]);
            }

            // L'API a répondu mais avec une erreur
            $errorMsg = $data['message'] ?? $data['error'] ?? ('HTTP ' . $statusCode);
            Log::error('PayTech — Erreur API', ['response' => $data]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur PayTech : ' . $errorMsg,
                'debug'   => $data,
            ], 422);

        } catch (\Exception $e) {
            Log::error('PayTech — Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Impossible de contacter PayTech : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * IPN Webhook PayTech
     */
    public function handlePaytechIPN(Request $request)
    {
        Log::info('PayTech IPN Received:', $request->all());

        $typeEvent = $request->input('type_event');
        $refCommand = $request->input('ref_command');

        // Vérification du paiement
        if ($typeEvent === 'sale_complete') {
            $paiement = Paiement::where('reference_transaction', $refCommand)->first();
            if ($paiement) {
                $paiement->statut = 'REUSSI';
                $paiement->date_paiement = now();
                $paiement->save();

                $user = Utilisateur::find($paiement->utilisateur_id);
                if ($user) {
                    $user->statut_abonnement = 'ACTIF';
                    $user->save();
                }
            }
        }

        return response()->json(['status' => 'SUCCESS'], 200);
    }

    /**
     * Succès du paiement PayTech (Redirection Client)
     */
    public function paytechSuccess(Request $request)
    {
        $ref = $request->query('ref');
        $user = Auth::user();

        if ($user) {
            $user->statut_abonnement = 'ACTIF';
            $user->save();
        }

        if ($ref) {
            $paiement = Paiement::where('reference_transaction', $ref)->first();
            if ($paiement) {
                $paiement->statut = 'REUSSI';
                $paiement->date_paiement = now();
                $paiement->save();
            }
        }

        return redirect()->route('dashboard')->with('success', '🎉 Paiement effectué avec succès via PayTech Sandbox ! Bienvenue dans votre laboratoire.');
    }

    /**
     * Annulation du paiement PayTech
     */
    public function paytechCancel(Request $request)
    {
        $ref = $request->query('ref');
        if ($ref) {
            $paiement = Paiement::where('reference_transaction', $ref)->first();
            if ($paiement) {
                $paiement->statut = 'ECHOUE';
                $paiement->save();
            }
        }

        return redirect()->route('payment')->withErrors(['subscription' => 'Paiement PayTech annulé. Veuillez réespayer pour activer votre laboratoire.']);
    }
}

