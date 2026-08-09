<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'destinataire_id' => 'nullable|exists:utilisateurs,id'
        ]);

        $user = Auth::user();

        // Si le destinataire n'est pas précisé (ex: depuis le client vers l'admin)
        $destinataireId = $request->input('destinataire_id');

        $message = Message::create([
            'expediteur_id' => $user->id,
            'destinataire_id' => $destinataireId,
            'message' => $request->input('message'),
            'est_lu' => false,
        ]);

        // Pour la vue JS, on va renvoyer un format qui matche ce qu'attend le JS
        $timeStr = $message->created_at->format('H:i');
        
        return response()->json([
            'success' => true,
            'message' => [
                'sender' => $user->role === 'ADMIN' ? 'support' : 'client',
                'text' => $message->message,
                'time' => $timeStr,
            ]
        ]);
    }
}
