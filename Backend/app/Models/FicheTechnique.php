<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FicheTechnique extends Model
{
    protected $table = 'fiches_techniques';

    protected $fillable = [
        'ingredient_id',
        'nom_inci',
        'categorie_fonctionnelle',
        'solubilite',
        'temperature_incorporation',
        'ph_optimal_min',
        'ph_optimal_max',
        'precautions',
        'conseils_formulateur',
    ];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }
}
