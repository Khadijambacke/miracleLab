<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormuleIngredient extends Model
{
    protected $table = 'formule_ingredients';

    protected $fillable = [
        'formule_id',
        'ingredient_id',
        'pourcentage',
        'cout_par_kg',
        'grammes_calculs',
        'phase',
    ];

    protected $casts = [
        'pourcentage' => 'decimal:2',
        'cout_par_kg' => 'decimal:2',
        'grammes_calculs' => 'decimal:2',
    ];

    public function formule(): BelongsTo
    {
        return $this->belongsTo(Formule::class, 'formule_id');
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }
}
