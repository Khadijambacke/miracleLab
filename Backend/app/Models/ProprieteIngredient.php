<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProprieteIngredient extends Model
{
    protected $table = 'proprietes_ingredients';
    public $timestamps = false;

    protected $fillable = [
        'ingredient_id',
        'libelle',
    ];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }
}
