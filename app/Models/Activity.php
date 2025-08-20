<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'sous_action_id',
        'titre',
        'description',
        'date_debut',
        'date_fin',
        'duree_estimee',
        'priorite',
        'statut',
        'taux_avancement',
        'owner_id',
        'notes',
        'tags'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'duree_estimee' => 'integer',
        'taux_avancement' => 'decimal:2'
    ];

    // Constantes pour les priorités
    const PRIORITE_BASSE = 'basse';
    const PRIORITE_MOYENNE = 'moyenne';
    const PRIORITE_HAUTE = 'haute';
    const PRIORITE_CRITIQUE = 'critique';

    // Constantes pour les statuts
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINE = 'termine';
    const STATUT_BLOQUE = 'bloque';

    // Relations
    public function sousAction(): BelongsTo
    {
        return $this->belongsTo(SousAction::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Accesseurs
    public function getPrioriteColorAttribute(): string
    {
        return match($this->priorite) {
            self::PRIORITE_BASSE => '#28a745',
            self::PRIORITE_MOYENNE => '#ffc107',
            self::PRIORITE_HAUTE => '#fd7e14',
            self::PRIORITE_CRITIQUE => '#dc3545',
            default => '#6c757d'
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE => '#6c757d',
            self::STATUT_EN_COURS => '#007bff',
            self::STATUT_TERMINE => '#28a745',
            self::STATUT_BLOQUE => '#dc3545',
            default => '#6c757d'
        };
    }

    public function getDureeRestanteAttribute(): int
    {
        if ($this->date_fin && $this->date_fin->isFuture()) {
            return Carbon::now()->diffInHours($this->date_fin, false);
        }
        return 0;
    }

    public function getEstEnRetardAttribute(): bool
    {
        return $this->date_fin && $this->date_fin->isPast() && $this->statut !== self::STATUT_TERMINE;
    }

    /**
     * Vérifier si l'activité a commencé (date de début <= aujourd'hui)
     */
    public function getACommenceAttribute(): bool
    {
        return $this->date_debut && $this->date_debut->lte(Carbon::today());
    }

    /**
     * Vérifier si l'activité peut être modifiée (progression et statut)
     */
    public function getPeutEtreModifieeAttribute(): bool
    {
        return $this->a_commence;
    }

    // Scopes
    public function scopeBySousAction($query, $sousActionId)
    {
        return $query->where('sous_action_id', $sousActionId);
    }

    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeByPriorite($query, $priorite)
    {
        return $query->where('priorite', $priorite);
    }

    public function scopeEnRetard($query)
    {
        return $query->where('date_fin', '<', Carbon::now())
                    ->where('statut', '!=', self::STATUT_TERMINE);
    }

    // Méthodes
    public function updateTauxAvancement($nouveauTaux): void
    {
        // Vérifier si l'activité peut être modifiée
        if (!$this->peut_etre_modifiee) {
            throw new \Exception('Impossible de modifier la progression d\'une activité qui n\'a pas encore commencé');
        }

        // Déterminer le statut automatiquement selon la progression
        $nouveauStatut = $this->determinerStatutParProgression($nouveauTaux);
        
        // Mettre à jour avec DB::table pour éviter les événements Eloquent
        DB::table('activities')
            ->where('id', $this->id)
            ->update([
                'taux_avancement' => $nouveauTaux,
                'statut' => $nouveauStatut
            ]);
        
        // Mettre à jour l'instance locale
        $this->taux_avancement = $nouveauTaux;
        $this->statut = $nouveauStatut;
        
        // Recalculer le taux de la sous-action parent
        $this->sousAction->recalculerTauxAvancement();
    }

    /**
     * Mettre à jour automatiquement le statut basé sur la date de début
     */
    public function updateStatutAutomatique(): void
    {
        $nouveauStatut = $this->determinerStatutParDate();
        
        if ($nouveauStatut !== $this->statut) {
            DB::table('activities')
                ->where('id', $this->id)
                ->update(['statut' => $nouveauStatut]);
            
            $this->statut = $nouveauStatut;
        }
    }
    
    /**
     * Déterminer le statut automatiquement selon la progression
     */
    public function determinerStatutParProgression($taux): string
    {
        if ($taux >= 100) {
            return self::STATUT_TERMINE;
        } elseif ($taux >= 1) {
            return self::STATUT_EN_COURS;
        } else {
            return self::STATUT_EN_ATTENTE;
        }
    }

    /**
     * Déterminer le statut automatiquement selon la date de début
     */
    public function determinerStatutParDate(): string
    {
        if (!$this->date_debut) {
            return self::STATUT_EN_ATTENTE;
        }

        $aujourdhui = Carbon::today();
        $dateDebut = Carbon::parse($this->date_debut)->startOfDay();

        if ($dateDebut->gt($aujourdhui)) {
            // Date de début dans le futur
            return self::STATUT_EN_ATTENTE;
        } else {
            // Date de début aujourd'hui ou dans le passé
            return self::STATUT_EN_COURS;
        }
    }

    public function getPrioritesList(): array
    {
        return [
            self::PRIORITE_BASSE => 'Basse',
            self::PRIORITE_MOYENNE => 'Moyenne',
            self::PRIORITE_HAUTE => 'Haute',
            self::PRIORITE_CRITIQUE => 'Critique'
        ];
    }

    public function getStatutsList(): array
    {
        return [
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_TERMINE => 'Terminé',
            self::STATUT_BLOQUE => 'Bloqué'
        ];
    }
}

