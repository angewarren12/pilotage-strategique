<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectifStrategique extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'pilier_id',
        'owner_id',
        'taux_avancement',
        'actif'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pilier()
    {
        return $this->belongsTo(Pilier::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function objectifsSpecifiques()
    {
        return $this->hasMany(ObjectifSpecifique::class);
    }

    /**
     * Calcule le taux d'avancement en temps réel basé sur les objectifs spécifiques
     */
    public function getTauxAvancementAttribute()
    {
        $objectifsSpecifiques = $this->objectifsSpecifiques;
        
        if ($objectifsSpecifiques->isEmpty()) {
            return 0;
        }

        $totalTaux = 0;
        $count = 0;

        foreach ($objectifsSpecifiques as $objectifSpecifique) {
            // Utiliser le calcul en temps réel de l'objectif spécifique
            $taux = $objectifSpecifique->getCalculatedTauxAvancement();
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
     * Mettre à jour le taux d'avancement du pilier parent
     */
    public function updateTauxAvancement(): void
    {
        if ($this->pilier) {
            $this->pilier->updateTauxAvancement();
        }
    }

    /**
     * Obtient le code complet (Pilier.OS)
     */
    public function getCodeCompletAttribute(): string
    {
        if ($this->pilier) {
            return $this->pilier->code . '.' . $this->code;
        }
        return $this->code;
    }

    // Événements
    protected static function booted()
    {
        static::saved(function ($objectifStrategique) {
            // Mettre à jour le taux d'avancement du pilier parent
            $objectifStrategique->updateTauxAvancement();
            
            // Vérifier si le taux d'avancement a changé (calculé automatiquement)
            $currentTaux = $objectifStrategique->getTauxAvancementAttribute();
            $oldTaux = $objectifStrategique->getOriginal('taux_avancement') ?? 0;
            
            if (abs($currentTaux - $oldTaux) > 0.01) { // Tolérance de 0.01%
                // Créer une notification de changement d'avancement
                app(\App\Services\NotificationService::class)->notifyAvancementChange(
                    'objectif_strategique',
                    $objectifStrategique->id,
                    $oldTaux,
                    $currentTaux,
                    $objectifStrategique
                );
            }
        });
    }
}
