<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectifSpecifique extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'objectif_strategique_id',
        'owner_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function objectifStrategique()
    {
        return $this->belongsTo(ObjectifStrategique::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    /**
     * Calcule le taux d'avancement en temps réel basé sur les actions
     */
    public function getTauxAvancementAttribute()
    {
        $actions = $this->actions;
        
        if ($actions->isEmpty()) {
            return 0;
        }

        $totalTaux = 0;
        $count = 0;

        foreach ($actions as $action) {
            // Utiliser le calcul en temps réel de l'action
            $taux = $action->getCalculatedTauxAvancement();
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

    /**
     * Obtient le code complet (Pilier.OS.OSPEC)
     */
    public function getCodeCompletAttribute()
    {
        if ($this->objectifStrategique && $this->objectifStrategique->pilier) {
            return $this->objectifStrategique->pilier->code . '.' . 
                   $this->objectifStrategique->code . '.' . 
                   $this->code;
        }
        return $this->code;
    }

    /**
     * Mettre à jour le taux d'avancement de l'objectif stratégique parent
     */
    public function updateTauxAvancement(): void
    {
        if ($this->objectifStrategique) {
            $this->objectifStrategique->updateTauxAvancement();
        }
    }

    // Événements
    protected static function booted()
    {
        static::saved(function ($objectifSpecifique) {
            // Mettre à jour le taux d'avancement de l'objectif stratégique parent
            $objectifSpecifique->updateTauxAvancement();
            
            // Vérifier si le taux d'avancement a changé (calculé automatiquement)
            $currentTaux = $objectifSpecifique->getTauxAvancementAttribute();
            $oldTaux = $objectifSpecifique->getOriginal('taux_avancement') ?? 0;
            
            if (abs($currentTaux - $oldTaux) > 0.01) { // Tolérance de 0.01%
                // Créer une notification de changement d'avancement
                app(\App\Services\NotificationService::class)->notifyAvancementChange(
                    'objectif_specifique',
                    $objectifSpecifique->id,
                    $oldTaux,
                    $currentTaux,
                    $objectifSpecifique
                );
            }
        });
    }

    // Scopes
    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }
}
