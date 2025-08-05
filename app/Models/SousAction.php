<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SousAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'libelle',
        'description',
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

    // Relations
    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
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
        // Temporairement désactivé pour éviter les boucles infinies
        /*
        static::saving(function ($sousAction) {
            $sousAction->calculerEcart();
            $sousAction->updateStatut();
        });

        static::saved(function ($sousAction) {
            $sousAction->updateTauxAvancement();
        });
        */
    }
}
