<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'action_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur qui a écrit le commentaire
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'action sur laquelle porte le commentaire
     */
    public function action()
    {
        return $this->belongsTo(Action::class);
    }

    /**
     * Scope pour trier par date de création (plus récent en premier)
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Accesseur pour formater la date de création
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y à H:i');
    }

    /**
     * Accesseur pour obtenir le nom court de l'utilisateur
     */
    public function getUserShortNameAttribute()
    {
        return $this->user ? $this->user->name : 'Utilisateur inconnu';
    }

    // Événements
    protected static function booted()
    {
        static::created(function ($comment) {
            // Créer une notification pour un nouveau commentaire
            app(\App\Services\NotificationService::class)->notifyCommentNew(
                'action',
                $comment->action_id,
                $comment->user->name ?? 'Utilisateur inconnu',
                $comment->action
            );
        });
    }
}
