<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SqueletteComposition extends Model
{
    protected $table = 'squelettes_compositions';

    protected $fillable = [
        'type_produit_id',
        'ingredient_id',
        'phase',
        'pourcentage_defaut',
        'ordre',
    ];

    public function typeProduit(): BelongsTo
    {
        return $this->belongsTo(TypeProduit::class, 'type_produit_id');
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }
}
