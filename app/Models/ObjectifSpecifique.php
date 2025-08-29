<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
     * ATTENTION: Cette méthode peut causer des boucles infinies
     * Utiliser avec précaution et éviter les appels récursifs
     */
    public function updateTauxAvancement(): void
    {
        try {
            if ($this->objectifStrategique) {
                // Calculer le nouveau taux basé sur les actions
                $actions = $this->actions;
                if ($actions->count() > 0) {
                    $totalProgress = 0;
                    $count = 0;
                    
                    foreach ($actions as $action) {
                        $taux = $action->getCalculatedTauxAvancement();
                        if ($taux !== null) {
                            $totalProgress += $taux;
                            $count++;
                        }
                    }
                    
                    if ($count > 0) {
                        $averageProgress = $totalProgress / $count;
                        
                        // Mettre à jour le taux local
                        $this->taux_avancement = round($averageProgress, 2);
                        $this->save();
                        
                        Log::info('✅ Taux d\'avancement OSP mis à jour', [
                            'osp_id' => $this->id,
                            'new_progress' => $this->taux_avancement
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('⚠️ Erreur mise à jour OSP', [
                'error' => $e->getMessage(), 
                'osp_id' => $this->id
            ]);
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

    /**
     * Obtient la date d'échéance maximale parmi toutes les sous-actions liées à cet objectif spécifique
     * @return string|null Date d'échéance maximale au format Y-m-d ou null si aucune
     */
    public function getMaxEcheanceDate()
    {
        try {
            // Utiliser une requête directe pour éviter les problèmes de relations complexes
            $maxEcheance = \DB::table('sous_actions')
                ->join('actions', 'sous_actions.action_id', '=', 'actions.id')
                ->where('actions.objectif_specifique_id', $this->id)
                ->whereNotNull('sous_actions.date_echeance')
                ->max('sous_actions.date_echeance');
            
            return $maxEcheance;
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors du calcul de la date d\'échéance maximale de l\'objectif spécifique', [
                'objectif_specifique_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
