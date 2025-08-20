<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'priority',
        'channel',
        'is_sent',
        'sent_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_sent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    // Méthodes
    public function markAsRead()
    {
        $this->update(['read_at' => Carbon::now()]);
    }

    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            'normal' => 'info',
            'low' => 'secondary',
            default => 'info'
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'avancement_change' => 'fas fa-chart-line',
            'echeance_approche' => 'fas fa-clock',
            'delai_depasse' => 'fas fa-exclamation-triangle',
            'comment_new' => 'fas fa-comments',
            'validation_required' => 'fas fa-check-circle',
            default => 'fas fa-bell'
        };
    }

    // Méthodes statiques pour créer des notifications
    public static function createAvancementChange($userId, $elementType, $elementId, $oldValue, $newValue)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'avancement_change',
            'title' => 'Changement d\'avancement',
            'message' => "L'avancement de {$elementType} a changé de {$oldValue}% à {$newValue}%",
            'data' => [
                'element_type' => $elementType,
                'element_id' => $elementId,
                'old_value' => $oldValue,
                'new_value' => $newValue
            ],
            'priority' => 'normal'
        ]);
    }

    public static function createEcheanceApproche($userId, $elementType, $elementId, $daysLeft)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'echeance_approche',
            'title' => 'Échéance approche',
            'message' => "L'échéance de {$elementType} approche (dans {$daysLeft} jours)",
            'data' => [
                'element_type' => $elementType,
                'element_id' => $elementId,
                'days_left' => $daysLeft
            ],
            'priority' => $daysLeft <= 3 ? 'high' : 'normal'
        ]);
    }

    public static function createDelaiDepasse($userId, $elementType, $elementId, $daysLate)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'delai_depasse',
            'title' => 'Délai dépassé',
            'message' => "Le délai de {$elementType} a été dépassé de {$daysLate} jours",
            'data' => [
                'element_type' => $elementType,
                'element_id' => $elementId,
                'days_late' => $daysLate
            ],
            'priority' => 'urgent'
        ]);
    }

    public static function createCommentNew($userId, $elementType, $elementId, $commentAuthor)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'comment_new',
            'title' => 'Nouveau commentaire',
            'message' => "{$commentAuthor} a ajouté un commentaire sur {$elementType}",
            'data' => [
                'element_type' => $elementType,
                'element_id' => $elementId,
                'comment_author' => $commentAuthor
            ],
            'priority' => 'normal'
        ]);
    }

    public static function createValidationRequired($userId, $elementType, $elementId, $requestedBy)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'validation_required',
            'title' => 'Validation requise',
            'message' => "{$requestedBy} demande votre validation pour {$elementType}",
            'data' => [
                'element_type' => $elementType,
                'element_id' => $elementId,
                'requested_by' => $requestedBy
            ],
            'priority' => 'high'
        ]);
    }
}
