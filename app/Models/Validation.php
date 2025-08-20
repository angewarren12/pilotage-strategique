<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Validation extends Model
{
    use HasFactory;

    protected $fillable = [
        'element_type',
        'element_id',
        'requested_by',
        'validated_by',
        'status',
        'comments',
        'rejection_reason',
        'requested_at',
        'validated_at',
        'expires_at',
        'validation_data'
    ];

    protected $casts = [
        'validation_data' => 'array',
        'requested_at' => 'datetime',
        'validated_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByElement($query, $elementType, $elementId)
    {
        return $query->where('element_type', $elementType)
                    ->where('element_id', $elementId);
    }

    public function scopeByRequester($query, $userId)
    {
        return $query->where('requested_by', $userId);
    }

    public function scopeByValidator($query, $userId)
    {
        return $query->where('validated_by', $userId);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', Carbon::now());
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>=', Carbon::now());
        });
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    // Méthodes utilitaires
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canBeValidatedBy(User $user): bool
    {
        // Logique pour déterminer si l'utilisateur peut valider
        // Basé sur la hiérarchie et les rôles
        return $this->getValidatorsForElement($this->element_type, $this->element_id)
                   ->contains('id', $user->id);
    }

    public function approve(User $validator, string $comments = null): bool
    {
        if (!$this->canBeValidatedBy($validator)) {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'validated_by' => $validator->id,
            'validated_at' => Carbon::now(),
            'comments' => $comments
        ]);

        // Créer une notification
        app(\App\Services\NotificationService::class)->notifyValidationRequired(
            $this->element_type,
            $this->element_id,
            $this->requestedBy->name ?? 'Utilisateur inconnu',
            $this->getElement()
        );

        return true;
    }

    public function reject(User $validator, string $reason): bool
    {
        if (!$this->canBeValidatedBy($validator)) {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'validated_by' => $validator->id,
            'validated_at' => Carbon::now(),
            'rejection_reason' => $reason
        ]);

        // Créer une notification
        app(\App\Services\NotificationService::class)->notifyValidationRequired(
            $this->element_type,
            $this->element_id,
            $this->requestedBy->name ?? 'Utilisateur inconnu',
            $this->getElement()
        );

        return true;
    }

    public function getElement()
    {
        switch ($this->element_type) {
            case 'pilier':
                return Pilier::find($this->element_id);
            case 'objectif_strategique':
                return ObjectifStrategique::find($this->element_id);
            case 'objectif_specifique':
                return ObjectifSpecifique::find($this->element_id);
            case 'action':
                return Action::find($this->element_id);
            case 'sous_action':
                return SousAction::find($this->element_id);
            default:
                return null;
        }
    }

    public function getElementName(): string
    {
        $element = $this->getElement();
        return $element ? $element->libelle : 'Élément inconnu';
    }

    public function getElementCode(): string
    {
        $element = $this->getElement();
        return $element ? $element->code : 'N/A';
    }

    public function getFormattedRequestedAtAttribute(): string
    {
        return $this->requested_at->diffForHumans();
    }

    public function getFormattedValidatedAtAttribute(): string
    {
        return $this->validated_at ? $this->validated_at->diffForHumans() : 'Non validé';
    }

    public function getStatusColorAttribute(): string
    {
        switch ($this->status) {
            case 'approved':
                return 'success';
            case 'rejected':
                return 'danger';
            case 'pending':
                return $this->isExpired() ? 'warning' : 'primary';
            default:
                return 'secondary';
        }
    }

    public function getStatusIconAttribute(): string
    {
        switch ($this->status) {
            case 'approved':
                return 'fas fa-check-circle';
            case 'rejected':
                return 'fas fa-times-circle';
            case 'pending':
                return $this->isExpired() ? 'fas fa-clock text-warning' : 'fas fa-clock';
            default:
                return 'fas fa-question-circle';
        }
    }

    // Méthodes statiques pour la gestion des validations
    public static function createValidationRequest(
        string $elementType,
        int $elementId,
        User $requester,
        array $validationData = [],
        int $expirationDays = 7
    ): self {
        return self::create([
            'element_type' => $elementType,
            'element_id' => $elementId,
            'requested_by' => $requester->id,
            'status' => 'pending',
            'validation_data' => $validationData,
            'expires_at' => Carbon::now()->addDays($expirationDays)
        ]);
    }

    public static function getValidatorsForElement(string $elementType, int $elementId): \Illuminate\Support\Collection
    {
        // Logique pour déterminer qui peut valider selon la hiérarchie
        $element = self::getElementByType($elementType, $elementId);
        
        if (!$element) {
            return collect();
        }

        $validators = collect();

        // Ajouter les validateurs selon la hiérarchie
        switch ($elementType) {
            case 'sous_action':
                // Le propriétaire de l'action parent peut valider
                if ($element->action && $element->action->owner) {
                    $validators->push($element->action->owner);
                }
                // Le propriétaire de l'objectif spécifique parent peut valider
                if ($element->action && $element->action->objectifSpecifique && $element->action->objectifSpecifique->owner) {
                    $validators->push($element->action->objectifSpecifique->owner);
                }
                break;
                
            case 'action':
                // Le propriétaire de l'objectif spécifique parent peut valider
                if ($element->objectifSpecifique && $element->objectifSpecifique->owner) {
                    $validators->push($element->objectifSpecifique->owner);
                }
                break;
                
            case 'objectif_specifique':
                // Le propriétaire de l'objectif stratégique parent peut valider
                if ($element->objectifStrategique && $element->objectifStrategique->owner) {
                    $validators->push($element->objectifStrategique->owner);
                }
                break;
                
            case 'objectif_strategique':
                // Le propriétaire du pilier parent peut valider
                if ($element->pilier && $element->pilier->owner) {
                    $validators->push($element->pilier->owner);
                }
                break;
                
            case 'pilier':
                // Les admins généraux peuvent valider
                $validators = User::whereHas('role', function($query) {
                    $query->where('nom', 'admin_general');
                })->get();
                break;
        }

        // Ajouter les admins généraux pour tous les types
        $admins = User::whereHas('role', function($query) {
            $query->where('nom', 'admin_general');
        })->get();
        
        $validators = $validators->merge($admins)->unique('id');

        return $validators;
    }

    private static function getElementByType(string $elementType, int $elementId)
    {
        switch ($elementType) {
            case 'pilier':
                return Pilier::find($elementId);
            case 'objectif_strategique':
                return ObjectifStrategique::find($elementId);
            case 'objectif_specifique':
                return ObjectifSpecifique::find($elementId);
            case 'action':
                return Action::find($elementId);
            case 'sous_action':
                return SousAction::find($elementId);
            default:
                return null;
        }
    }
}
