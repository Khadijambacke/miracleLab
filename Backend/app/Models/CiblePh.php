<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CiblePh extends Model
{
    protected $table = 'cibles_ph';

    protected $fillable = [
        'type_produit_id',
        'ph_min',
        'ph_max',
    ];

    public function typeProduit(): BelongsTo
    {
        return $this->belongsTo(TypeProduit::class, 'type_produit_id');
    }
}
