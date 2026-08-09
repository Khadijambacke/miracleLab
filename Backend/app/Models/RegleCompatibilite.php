<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegleCompatibilite extends Model
{
    protected $table = 'regles_compatibilite';

    protected $fillable = [
        'nom_regle',
        'groupe_a',
        'groupe_b',
        'niveau',
        'message_alerte',
    ];

    protected $casts = [
        'groupe_a' => 'array',
        'groupe_b' => 'array',
    ];
}
