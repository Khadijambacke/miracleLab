<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $table = 'ingredients';

    protected $fillable = [
        'utilisateur_id',
        'categorie_id',
        'nom',
        'phase',
        'nom_groupe',
        'note',
        'pourcentage_min',
        'pourcentage_max',
        'impact_ph',
        'est_personnalise',
        'inci',
        'solubilite',
        'precautions',
        'conseils',
    ];

    protected $casts = [
        'est_personnalise' => 'boolean',
        'pourcentage_min' => 'decimal:2',
        'pourcentage_max' => 'decimal:2',
        'impact_ph' => 'decimal:1',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(CategorieIngredient::class, 'categorie_id');
    }

    public function ficheTechnique(): HasOne
    {
        return $this->hasOne(FicheTechnique::class, 'ingredient_id');
    }

    public function proprietes(): HasMany
    {
        return $this->hasMany(ProprieteIngredient::class, 'ingredient_id');
    }
}
