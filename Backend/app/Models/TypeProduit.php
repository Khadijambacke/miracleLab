<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TypeProduit extends Model
{
    protected $table = 'types_produits';

    protected $fillable = [
        'utilisateur_id',
        'nom',
        'code',
        'categorie',
        'squelette',
    ];

    protected $casts = [
        'squelette' => 'array',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
