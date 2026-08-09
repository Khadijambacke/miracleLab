<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueActivite extends Model
{
    protected $table = 'historique_activites';
    public $timestamps = false;

    protected $fillable = [
        'utilisateur_id',
        'action',
        'objet_type',
        'objet_id',
        'donnees_avant',
        'donnees_apres',
        'created_at',
    ];

    protected $casts = [
        'donnees_avant' => 'array',
        'donnees_apres' => 'array',
        'created_at' => 'datetime',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
