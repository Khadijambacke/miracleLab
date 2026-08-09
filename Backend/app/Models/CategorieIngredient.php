<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieIngredient extends Model
{
    protected $table = 'categories_ingredients';

    protected $fillable = [
        'nom',
        'description',
    ];

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class, 'categorie_id');
    }
}
