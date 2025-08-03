<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'objectif_specifique_id',
        'owner_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function objectifSpecifique()
    {
        return $this->belongsTo(ObjectifSpecifique::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function sousActions()
    {
        return $this->hasMany(SousAction::class);
    }

    /**
     * Calcule le taux d'avancement en temps réel basé sur les sous-actions
     */
    public function getTauxAvancementAttribute()
    {
        $sousActions = $this->sousActions;
        
        if ($sousActions->isEmpty()) {
            return 0;
        }

        $totalTaux = 0;
        $count = 0;

        foreach ($sousActions as $sousAction) {
            // Utiliser le taux d'avancement direct de la sous-action (pas de récursion)
            $taux = $sousAction->getAttribute('taux_avancement');
            if ($taux !== null) {
                $totalTaux += $taux;
                $count++;
            }
        }

        return $count > 0 ? round($totalTaux / $count, 2) : 0;
    }

    /**
     * Méthode pour obtenir le taux d'avancement calculé sans récursion
     */
    public function getCalculatedTauxAvancement()
    {
        return $this->getTauxAvancementAttribute();
    }
}
