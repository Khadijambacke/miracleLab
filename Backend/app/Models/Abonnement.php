<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Abonnement extends Model
{
    protected $table = 'abonnements';

    protected $fillable = [
        'utilisateur_id',
        'montant',
        'devise',
        'methode_paiement',
        'reference_transaction',
        'statut',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
