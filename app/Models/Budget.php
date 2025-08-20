<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Budget extends Model
{
    protected $fillable = [
        'pilier_id',
        'objectif_strategique_id',
        'objectif_specifique_id',
        'action_id',
        'sous_action_id',
        'montant_alloue',
        'montant_engage',
        'montant_realise',
        'montant_restant',
        'annee_budgetaire',
        'date_debut',
        'date_fin',
        'statut',
        'type_budget',
        'code_budget',
        'description',
        'justification',
        'source_financement',
        'owner_id',
        'validated_by',
        'validated_at',
        'seuil_alerte',
        'seuil_critique',
        'alertes_actives',
    ];

    protected $casts = [
        'montant_alloue' => 'decimal:2',
        'montant_engage' => 'decimal:2',
        'montant_realise' => 'decimal:2',
        'montant_restant' => 'decimal:2',
        'annee_budgetaire' => 'integer',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'validated_at' => 'datetime',
        'seuil_alerte' => 'decimal:2',
        'seuil_critique' => 'decimal:2',
        'alertes_actives' => 'boolean',
    ];

    // Relations hiérarchiques
    public function pilier(): BelongsTo
    {
        return $this->belongsTo(Pilier::class);
    }

    public function objectifStrategique(): BelongsTo
    {
        return $this->belongsTo(ObjectifStrategique::class);
    }

    public function objectifSpecifique(): BelongsTo
    {
        return $this->belongsTo(ObjectifSpecifique::class);
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }

    public function sousAction(): BelongsTo
    {
        return $this->belongsTo(SousAction::class);
    }

    // Relations utilisateurs
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Scopes
    public function scopeActif(Builder $query): Builder
    {
        return $query->where('statut', 'actif');
    }

    public function scopeByAnnee(Builder $query, int $annee): Builder
    {
        return $query->where('annee_budgetaire', $annee);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type_budget', $type);
    }

    public function scopeByOwner(Builder $query, int $ownerId): Builder
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeByPilier(Builder $query, int $pilierId): Builder
    {
        return $query->where('pilier_id', $pilierId);
    }

    public function scopeByObjectifStrategique(Builder $query, int $objectifStrategiqueId): Builder
    {
        return $query->where('objectif_strategique_id', $objectifStrategiqueId);
    }

    public function scopeByObjectifSpecifique(Builder $query, int $objectifSpecifiqueId): Builder
    {
        return $query->where('objectif_specifique_id', $objectifSpecifiqueId);
    }

    public function scopeByAction(Builder $query, int $actionId): Builder
    {
        return $query->where('action_id', $actionId);
    }

    public function scopeBySousAction(Builder $query, int $sousActionId): Builder
    {
        return $query->where('sous_action_id', $sousActionId);
    }

    // Méthodes de calcul
    public function getTauxEngagementAttribute(): float
    {
        if ($this->montant_alloue > 0) {
            return round(($this->montant_engage / $this->montant_alloue) * 100, 2);
        }
        return 0;
    }

    public function getTauxRealisationAttribute(): float
    {
        if ($this->montant_alloue > 0) {
            return round(($this->montant_realise / $this->montant_alloue) * 100, 2);
        }
        return 0;
    }

    public function getTauxRestantAttribute(): float
    {
        if ($this->montant_alloue > 0) {
            return round(($this->montant_restant / $this->montant_alloue) * 100, 2);
        }
        return 0;
    }

    // Méthodes d'alerte
    public function isAlerteEngagement(): bool
    {
        return $this->alertes_actives && $this->taux_engagement >= $this->seuil_alerte;
    }

    public function isAlerteCritique(): bool
    {
        return $this->alertes_actives && $this->taux_engagement >= $this->seuil_critique;
    }

    public function isDepassement(): bool
    {
        return $this->montant_engage > $this->montant_alloue;
    }

    // Méthodes de validation
    public function isValide(): bool
    {
        return !is_null($this->validated_at);
    }

    public function valider(User $user): bool
    {
        return $this->update([
            'validated_by' => $user->id,
            'validated_at' => now(),
        ]);
    }

    // Méthodes de mise à jour automatique
    public function updateMontantRestant(): void
    {
        $this->montant_restant = $this->montant_alloue - $this->montant_engage;
        $this->save();
    }

    public function engagerMontant(float $montant): bool
    {
        if ($this->montant_restant >= $montant) {
            $this->montant_engage += $montant;
            $this->updateMontantRestant();
            return true;
        }
        return false;
    }

    public function realiserMontant(float $montant): bool
    {
        if ($this->montant_engage >= $montant) {
            $this->montant_realise += $montant;
            $this->save();
            return true;
        }
        return false;
    }

    // Méthodes d'identification
    public function getElementTypeAttribute(): string
    {
        if ($this->pilier_id) return 'pilier';
        if ($this->objectif_strategique_id) return 'objectif_strategique';
        if ($this->objectif_specifique_id) return 'objectif_specifique';
        if ($this->action_id) return 'action';
        if ($this->sous_action_id) return 'sous_action';
        return 'unknown';
    }

    public function getElementAttribute()
    {
        switch ($this->element_type) {
            case 'pilier':
                return $this->pilier;
            case 'objectif_strategique':
                return $this->objectifStrategique;
            case 'objectif_specifique':
                return $this->objectifSpecifique;
            case 'action':
                return $this->action;
            case 'sous_action':
                return $this->sousAction;
            default:
                return null;
        }
    }

    public function getElementNameAttribute(): string
    {
        $element = $this->element;
        return $element ? $element->libelle ?? $element->name ?? 'N/A' : 'N/A';
    }

    public function getElementCodeAttribute(): string
    {
        $element = $this->element;
        return $element ? $element->code ?? 'N/A' : 'N/A';
    }

    // Méthodes de formatage
    public function getFormattedMontantAlloueAttribute(): string
    {
        return number_format($this->montant_alloue, 2, ',', ' ') . ' €';
    }

    public function getFormattedMontantEngageAttribute(): string
    {
        return number_format($this->montant_engage, 2, ',', ' ') . ' €';
    }

    public function getFormattedMontantRealiseAttribute(): string
    {
        return number_format($this->montant_realise, 2, ',', ' ') . ' €';
    }

    public function getFormattedMontantRestantAttribute(): string
    {
        return number_format($this->montant_restant, 2, ',', ' ') . ' €';
    }

    // Méthodes de statut
    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            'actif' => 'success',
            'inactif' => 'warning',
            'archive' => 'secondary',
            default => 'primary'
        };
    }

    public function getTypeBudgetColorAttribute(): string
    {
        return match($this->type_budget) {
            'investissement' => 'primary',
            'fonctionnement' => 'success',
            'personnel' => 'info',
            'autre' => 'warning',
            default => 'secondary'
        };
    }

    // Boot method pour les calculs automatiques
    protected static function booted()
    {
        static::saving(function ($budget) {
            // Calcul automatique du montant restant
            $budget->montant_restant = $budget->montant_alloue - $budget->montant_engage;
        });
    }
}
