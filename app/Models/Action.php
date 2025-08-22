<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
     * Relation avec les commentaires
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relation avec les commentaires triés par date (plus récent en premier)
     */
    public function commentsLatest()
    {
        return $this->hasMany(Comment::class)->latest();
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

    /**
     * Mettre à jour le taux d'avancement de l'objectif spécifique parent
     * ATTENTION: Cette méthode peut causer des boucles infinies
     * Utiliser avec précaution et éviter les appels récursifs
     */
    public function updateTauxAvancement(): void
    {
        try {
            if ($this->objectifSpecifique) {
                // Calculer le nouveau taux basé sur les sous-actions
                $sousActions = $this->sousActions;
                if ($sousActions->count() > 0) {
                    $totalProgress = $sousActions->sum('taux_avancement');
                    $averageProgress = $totalProgress / $sousActions->count();
                    
                    // Mettre à jour le taux local
                    $this->taux_avancement = round($averageProgress, 2);
                    $this->save();
                    
                    Log::info('✅ Taux d\'avancement Action mis à jour', [
                        'action_id' => $this->id,
                        'new_progress' => $this->taux_avancement
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('⚠️ Erreur mise à jour Action', [
                'error' => $e->getMessage(), 
                'action_id' => $this->id
            ]);
        }
    }

    /**
     * Obtient le code complet (Pilier.OS.OSPEC.ACTION)
     */
    public function getCodeCompletAttribute(): string
    {
        $code = $this->code;
        if ($this->objectifSpecifique) {
            $code = $this->objectifSpecifique->code_complet . '.' . $code;
        }
        return $code;
    }

    // Événements
    protected static function booted()
    {
        static::saved(function ($action) {
            // Mettre à jour le taux d'avancement de l'objectif spécifique parent
            $action->updateTauxAvancement();
            
            // Vérifier si le taux d'avancement a changé (calculé automatiquement)
            $currentTaux = $action->getTauxAvancementAttribute();
            $oldTaux = $action->getOriginal('taux_avancement') ?? 0;
            
            if (abs($currentTaux - $oldTaux) > 0.01) { // Tolérance de 0.01%
                // Créer une notification de changement d'avancement
                app(\App\Services\NotificationService::class)->notifyAvancementChange(
                    'action',
                    $action->id,
                    $oldTaux,
                    $currentTaux,
                    $action
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
