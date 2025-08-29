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

    /**
     * Accesseur pour s'assurer que taux_avancement est toujours un nombre
     */
    public function setTauxAvancementAttribute($value)
    {
        // S'assurer que la valeur est un nombre entre 0 et 100
        $value = floatval($value);
        $value = max(0, min(100, $value)); // Limiter entre 0 et 100
        $this->attributes['taux_avancement'] = round($value, 2);
    }

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

    public function updateStatut(): void
    {
        if ($this->taux_avancement >= 100) {
            $this->statut = 'termine';
        } elseif ($this->date_echeance && Carbon::now()->gt($this->date_echeance)) {
            $this->statut = 'en_retard';
        } else {
            $this->statut = 'en_cours';
        }
        
        $this->save();
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
        static::saving(function ($sousAction) {
            // Calculer l'écart et mettre à jour le statut
            $sousAction->calculerEcart();
            $sousAction->updateStatut();
        });

        static::saved(function ($sousAction) {
            // Mettre à jour le taux d'avancement de l'action parent
            $sousAction->updateTauxAvancement();
            
            // Vérifier si le taux d'avancement a changé
            if ($sousAction->wasChanged('taux_avancement')) {
                $oldValue = $sousAction->getOriginal('taux_avancement') ?? 0;
                $newValue = $sousAction->taux_avancement;
                
                // Créer une notification de changement d'avancement
                app(\App\Services\NotificationService::class)->notifyAvancementChange(
                    'sous_action',
                    $sousAction->id,
                    $oldValue,
                    $newValue,
                    $sousAction
                );
            }
            
            // Vérifier les échéances approchantes
            if ($sousAction->date_echeance && !$sousAction->date_realisation) {
                $daysLeft = now()->diffInDays($sousAction->date_echeance, false);
                
                if ($daysLeft >= 0 && $daysLeft <= 7) {
                    app(\App\Services\NotificationService::class)->notifyEcheanceApproche(
                        'sous_action',
                        $sousAction->id,
                        $daysLeft,
                        $sousAction
                    );
                }
                
                // Vérifier si le délai est dépassé
                if ($daysLeft < 0) {
                    app(\App\Services\NotificationService::class)->notifyDelaiDepasse(
                        'sous_action',
                        $sousAction->id,
                        abs($daysLeft),
                        $sousAction
                    );
                }
            }
        });
    }
}

