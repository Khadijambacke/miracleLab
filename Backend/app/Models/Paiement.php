<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'utilisateur_id',
        'montant',
        'statut',
        'methode_paiement',
        'reference_transaction',
        'date_paiement',
    ];
}
