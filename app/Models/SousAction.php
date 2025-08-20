<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SousAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'type',
        'action_id',
        'owner_id',
        'taux_avancement',
        'date_echeance',
        'date_realisation'
    ];

    protected $casts = [
        'taux_avancement' => 'decimal:2',
        'date_echeance' => 'date',
        'date_realisation' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constantes pour les types
    const TYPE_NORMAL = 'normal';
    const TYPE_PROJET = 'projet';

    // Relations
    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Calcule le taux d'avancement en temps réel (pour les sous-actions, c'est la valeur stockée)
     */
    public function getTauxAvancementAttribute()
    {
        return $this->attributes['taux_avancement'] ?? 0;
    }

    // Méthodes de calcul automatique
    public function calculerEcart(): int
    {
        if (!$this->date_echeance) {
            return 0;
        }

        $dateReference = $this->date_realisation ?? Carbon::now();
        $ecart = $dateReference->diffInDays($this->date_echeance, false);
        
        $this->update(['ecart_jours' => $ecart]);
        
        return $ecart;
    }

    /**
     * Met à jour le statut automatiquement basé sur le taux et la date d'échéance
     */
    public function updateStatut(): void
    {
        $nouveauStatut = $this->determinerStatut();
        
        if ($this->statut !== $nouveauStatut) {
            $this->statut = $nouveauStatut;
            Log::info('🔄 [STATUT] Statut mis à jour', [
                'sous_action_id' => $this->id,
                'ancien_statut' => $this->statut,
                'nouveau_statut' => $nouveauStatut,
                'taux_avancement' => $this->taux_avancement,
                'date_echeance' => $this->date_echeance
            ]);
        }
    }
    
    /**
     * Détermine le statut basé sur le taux et la date d'échéance
     */
    private function determinerStatut(): string
    {
        $taux = $this->taux_avancement ?? 0;
        $dateEcheance = $this->date_echeance;
        $aujourdhui = now();
        
        // Règles de statut
        if ($taux == 0) {
            return 'à_démarrer';
        }
        
        if ($taux == 100) {
            return 'termine';
        }
        
        if ($taux > 0 && $taux < 100) {
            // Vérifier si la date d'échéance est atteinte
            if ($dateEcheance && $aujourdhui->gt($dateEcheance)) {
                return 'en_retard';
        } else {
                return 'en_cours';
            }
        }
        
        // Par défaut
        return 'à_démarrer';
    }
    
    /**
     * Vérifie si la sous-action est en retard
     */
    public function isEnRetard(): bool
    {
        return $this->statut === 'en_retard';
    }
    
    /**
     * Vérifie si la sous-action est terminée
     */
    public function isTerminee(): bool
    {
        return $this->statut === 'termine';
    }
    
    /**
     * Vérifie si la sous-action est en cours
     */
    public function isEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }
    
    /**
     * Vérifie si la sous-action est à démarrer
     */
    public function isADemarrer(): bool
    {
        return $this->statut === 'à_démarrer';
    }

    public function updateTauxAvancement(): void
    {
        // Mettre à jour le taux d'avancement de l'action parent
        if ($this->action) {
            $this->action->updateTauxAvancement();
        }
    }

    public function recalculerTauxAvancement(): void
    {
        Log::info('🔄 [SOUSACTION] Début recalculerTauxAvancement', [
            'sous_action_id' => $this->id,
            'type' => $this->type,
            'step' => 'method_start'
        ]);

        try {
            if ($this->type === self::TYPE_PROJET && $this->activities()->exists()) {
                Log::info('🔄 [SOUSACTION] Type projet détecté, activités existent', [
                    'sous_action_id' => $this->id,
                    'step' => 'projet_type_check'
                ]);

                // Calculer la moyenne simple des taux d'avancement des activités
                $totalTaux = 0;
                $nombreActivites = $this->activities()->count();
                
                Log::info('🔄 [SOUSACTION] Comptage des activités', [
                    'sous_action_id' => $this->id,
                    'nombre_activites' => $nombreActivites,
                    'step' => 'count_activities'
                ]);
                
                if ($nombreActivites > 0) {
                    $totalTaux = $this->activities()->sum('taux_avancement');
                    $nouveauTaux = round($totalTaux / $nombreActivites, 2);
                    
                    Log::info('🔄 [SOUSACTION] Calcul du nouveau taux', [
                        'sous_action_id' => $this->id,
                        'total_taux' => $totalTaux,
                        'nouveau_taux' => $nouveauTaux,
                        'step' => 'taux_calculation'
                    ]);
                    
                    Log::info('🔄 [SOUSACTION] Tentative de mise à jour', [
                        'sous_action_id' => $this->id,
                        'data_to_update' => ['taux_avancement' => $nouveauTaux],
                        'step' => 'update_attempt'
                    ]);
                    
                    try {
                        Log::info('🔄 [SOUSACTION] Utilisation de DB::table pour contourner les événements Eloquent', [
                            'sous_action_id' => $this->id,
                            'step' => 'db_table_attempt'
                        ]);
                        
                        // Utiliser DB::table pour contourner les événements Eloquent
                        \DB::table('sous_actions')
                            ->where('id', $this->id)
                            ->update(['taux_avancement' => $nouveauTaux]);
                        
                        Log::info('✅ [SOUSACTION] Taux mis à jour avec succès via DB::table', [
                            'sous_action_id' => $this->id,
                            'step' => 'update_success'
                        ]);
                    } catch (\Exception $e) {
                        Log::error('💥 [SOUSACTION] Erreur lors de la mise à jour via DB::table', [
                            'sous_action_id' => $this->id,
                            'error_message' => $e->getMessage(),
                            'error_file' => $e->getFile(),
                            'error_line' => $e->getLine(),
                            'step' => 'update_error'
                        ]);
                        throw $e;
                    }
                }
                
                // Cascade vers les niveaux parents (avec vérification de sécurité)
                if ($this->action && method_exists($this->action, 'recalculerTauxAvancement')) {
                    Log::info('🔄 [SOUSACTION] Cascade vers action parent', [
                        'sous_action_id' => $this->id,
                        'action_id' => $this->action->id,
                        'step' => 'cascade_start'
                    ]);
                    
                    try {
                        $this->action->recalculerTauxAvancement();
                        
                        Log::info('✅ [SOUSACTION] Cascade vers action parent réussie', [
                            'sous_action_id' => $this->id,
                            'action_id' => $this->action->id,
                            'step' => 'cascade_success'
                        ]);
                    } catch (\Exception $e) {
                        // Log l'erreur mais continue
                        Log::warning('⚠️ [SOUSACTION] Erreur lors du recalcul du taux d\'avancement de l\'action parent', [
                            'action_id' => $this->action->id,
                            'error' => $e->getMessage(),
                            'error_file' => $e->getFile(),
                            'error_line' => $e->getLine(),
                            'step' => 'cascade_error'
                        ]);
                    }
                } else {
                    Log::info('ℹ️ [SOUSACTION] Pas de cascade vers action parent', [
                        'sous_action_id' => $this->id,
                        'has_action' => $this->action ? 'oui' : 'non',
                        'has_method' => $this->action && method_exists($this->action, 'recalculerTauxAvancement') ? 'oui' : 'non',
                        'step' => 'no_cascade'
                    ]);
                }
            } else {
                Log::info('ℹ️ [SOUSACTION] Pas de recalcul (type normal ou pas d\'activités)', [
                    'sous_action_id' => $this->id,
                    'type' => $this->type,
                    'has_activities' => $this->activities()->exists() ? 'oui' : 'non',
                    'step' => 'no_recalculation'
                ]);
            }
            
            Log::info('✅ [SOUSACTION] Fin recalculerTauxAvancement avec succès', [
                'sous_action_id' => $this->id,
                'step' => 'method_success'
            ]);
            
        } catch (\Exception $e) {
            Log::error('💥 [SOUSACTION] Erreur dans recalculerTauxAvancement', [
                'sous_action_id' => $this->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'step' => 'method_error'
            ]);
            
            // Relancer l'exception pour que le contrôleur puisse la gérer
            throw $e;
        }
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_NORMAL => 'Normal',
            self::TYPE_PROJET => 'Projet',
            default => 'Inconnu'
        };
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeByAction($query, $actionId)
    {
        return $query->where('action_id', $actionId);
    }

    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeEnRetard($query)
    {
        return $query->where('statut', 'en_retard');
    }

    public function scopeTermine($query)
    {
        return $query->where('statut', 'termine');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    // Méthodes utilitaires
    public function getCodeCompletAttribute(): string
    {
        $code = $this->code;
        if ($this->action) {
            $code = $this->action->code_complet . '.' . $code;
        }
        return $code;
    }

    public function getStatutColorAttribute(): string
    {
        switch ($this->statut) {
            case 'termine':
                return 'success'; // Vert
            case 'en_retard':
                return 'danger'; // Rouge
            case 'en_cours':
                if ($this->taux_avancement >= 75) {
                    return 'info'; // Bleu
                } elseif ($this->taux_avancement >= 50) {
                    return 'warning'; // Orange
                } else {
                    return 'secondary'; // Gris
                }
            default:
                return 'secondary';
        }
    }

    public function getStatutLibelleAttribute(): string
    {
        switch ($this->statut) {
            case 'termine':
                return 'Terminé';
            case 'en_retard':
                return 'En retard';
            case 'en_cours':
                return 'En cours';
            default:
                return 'Inconnu';
        }
    }

    public function getEcartLibelleAttribute(): string
    {
        if ($this->statut === 'termine') {
            return 'Terminé';
        }
        
        if ($this->ecart_jours === null) {
            return 'Non défini';
        }
        
        if ($this->ecart_jours > 0) {
            return $this->ecart_jours . ' jours d\'avance';
        } elseif ($this->ecart_jours < 0) {
            return abs($this->ecart_jours) . ' jours de retard';
        } else {
            return 'À jour';
        }
    }

    // Événements
    protected static function booted()
    {
        // ÉVÉNEMENT SAVING - MISE À JOUR DU STATUT ET ÉCART
        static::saving(function ($sousAction) {
            // Éviter la boucle infinie en vérifiant si on est déjà en train de sauvegarder
            // Utiliser les vraies méthodes Laravel
            if ($sousAction->exists && !$sousAction->isDirty(['statut', 'ecart_jours'])) {
                return; // Éviter la boucle
            }
            
            // Calculer l'écart seulement si la date d'échéance a changé
            if ($sousAction->isDirty(['date_echeance', 'date_realisation']) || $sousAction->isDirty('taux_avancement')) {
            $sousAction->calculerEcart();
            $sousAction->updateStatut();
            }
        });

        // ÉVÉNEMENT SAVED - CASCADE VERS LES PARENTS ET MISE À JOUR EN BASE
        static::saved(function ($sousAction) {
            // Mettre à jour les taux parents en base de données
            if ($sousAction->wasChanged('taux_avancement')) {
                $sousAction->updateTauxParentsEnBase();
            }
        });
        
        
    }
    
    /**
     * Met à jour les taux de tous les parents en base de données
     */
    public function updateTauxParentsEnBase()
    {
        try {
            Log::info('🔄 [CASCADE] Début de la mise à jour des taux parents en base', [
                'sous_action_id' => $this->id,
                'nouveau_taux' => $this->taux_avancement
            ]);
            
            // 1. Mettre à jour le taux de l'ACTION parent
            if ($this->action) {
                $this->updateTauxActionEnBase();
            }
            
            // 2. Mettre à jour le taux de l'OSP parent
            if ($this->action && $this->action->objectifSpecifique) {
                $this->updateTauxOSPEnBase();
            }
            
            // 3. Mettre à jour le taux de l'OS parent
            if ($this->action && $this->action->objectifSpecifique && $this->action->objectifSpecifique->objectifStrategique) {
                $this->updateTauxOSEnBase();
            }
            
            // 4. Mettre à jour le taux du PILIER parent
            if ($this->action && $this->action->objectifSpecifique && $this->action->objectifSpecifique->objectifStrategique && $this->action->objectifSpecifique->objectifStrategique->pilier) {
                $this->updateTauxPilierEnBase();
            }
            
            Log::info('✅ [CASCADE] Tous les taux parents ont été mis à jour en base');
            
        } catch (\Exception $e) {
            Log::error('❌ [CASCADE] Erreur lors de la mise à jour des taux parents', [
                'sous_action_id' => $this->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
    
    /**
     * Met à jour le taux de l'action parent en base
     */
    private function updateTauxActionEnBase()
    {
        $action = $this->action;
        $sousActions = $action->sousActions;
        
        if ($sousActions->count() > 0) {
            $totalTaux = $sousActions->sum('taux_avancement');
            $nouveauTaux = round($totalTaux / $sousActions->count(), 2);
            
            $action->taux_avancement = $nouveauTaux;
            $action->save();
            
            Log::info('✅ [CASCADE] Action mise à jour en base', [
                'action_id' => $action->id,
                'nouveau_taux' => $nouveauTaux
            ]);
        }
    }
    
    /**
     * Met à jour le taux de l'OSP parent en base
     */
    private function updateTauxOSPEnBase()
    {
        $osp = $this->action->objectifSpecifique;
        $actions = $osp->actions;
        
        if ($actions->count() > 0) {
            $totalTaux = 0;
            foreach ($actions as $action) {
                $totalTaux += $action->taux_avancement ?? 0;
            }
            $nouveauTaux = round($totalTaux / $actions->count(), 2);
            
            $osp->taux_avancement = $nouveauTaux;
            $osp->save();
            
            Log::info('✅ [CASCADE] OSP mis à jour en base', [
                'osp_id' => $osp->id,
                'nouveau_taux' => $nouveauTaux
            ]);
        }
    }
    
    /**
     * Met à jour le taux de l'OS parent en base
     */
    private function updateTauxOSEnBase()
    {
        $os = $this->action->objectifSpecifique->objectifStrategique;
        $objectifsSpecifiques = $os->objectifsSpecifiques;
        
        if ($objectifsSpecifiques->count() > 0) {
            $totalTaux = 0;
            foreach ($objectifsSpecifiques as $osp) {
                $totalTaux += $osp->taux_avancement ?? 0;
            }
            $nouveauTaux = round($totalTaux / $objectifsSpecifiques->count(), 2);
            
            $os->taux_avancement = $nouveauTaux;
            $os->save();
            
            Log::info('✅ [CASCADE] OS mis à jour en base', [
                'os_id' => $os->id,
                'nouveau_taux' => $nouveauTaux
            ]);
        }
    }
    
    /**
     * Met à jour le taux du pilier parent en base
     */
    private function updateTauxPilierEnBase()
    {
        $pilier = $this->action->objectifSpecifique->objectifStrategique->pilier;
        $objectifsStrategiques = $pilier->objectifsStrategiques;
        
        if ($objectifsStrategiques->count() > 0) {
            $totalTaux = 0;
            foreach ($objectifsStrategiques as $os) {
                $totalTaux += $os->taux_avancement ?? 0;
            }
            $nouveauTaux = round($totalTaux / $objectifsStrategiques->count(), 2);
            
            $pilier->taux_avancement = $nouveauTaux;
            $pilier->save();
            
            Log::info('✅ [CASCADE] Pilier mis à jour en base', [
                'pilier_id' => $pilier->id,
                'nouveau_taux' => $nouveauTaux
            ]);
        }
    }
}
