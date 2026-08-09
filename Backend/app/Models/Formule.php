<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formule extends Model
{
    protected $table = 'formules';

    protected $fillable = [
        'utilisateur_id',
        'nom',
        'categorie',
        'type_produit',
        'poids_lot',
        'notes',
        'ph_estime',
    ];

    protected $casts = [
        'poids_lot' => 'decimal:2',
        'ph_estime' => 'decimal:2',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function formuleIngredients(): HasMany
    {
        return $this->hasMany(FormuleIngredient::class, 'formule_id');
    }

    public function obtenirCoutTotal(): float
    {
        return $this->formuleIngredients->reduce(function ($total, $ing) {
            return $total + (($ing->cout_par_kg / 1000) * $ing->grammes_calculs);
        }, 0.0);
    }
}
