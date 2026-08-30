<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'utilisateurs';

    protected $fillable = [
        'nom_complet',
        'email',
        'telephone',
        'mot_de_passe',
        'role',
        'statut_abonnement',
        'type_plan',
        'date_expiration_abonnement',
    ];

    protected $casts = [
        'date_expiration_abonnement' => 'datetime',
    ];

    public function estAbonnementValide(): bool
    {
        if ($this->role === 'ADMIN') {
            return true;
        }

        if (strtoupper($this->statut_abonnement ?? '') !== 'ACTIF') {
            return false;
        }

        if ($this->date_expiration_abonnement && now()->greaterThan($this->date_expiration_abonnement)) {
            return false;
        }

        return true;
    }

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    // Indiquer à Laravel d'utiliser 'mot_de_passe' pour l'authentification
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    public function getAuthPasswordName()
    {
        return 'mot_de_passe';
    }

    public function formules(): HasMany
    {
        return $this->hasMany(Formule::class, 'utilisateur_id');
    }

    public function abonnements(): HasMany
    {
        return $this->hasMany(Abonnement::class, 'utilisateur_id');
    }

    public function ingredientsPersonnalises(): HasMany
    {
        return $this->hasMany(Ingredient::class, 'utilisateur_id');
    }
}
