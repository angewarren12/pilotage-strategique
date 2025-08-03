<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'libelle',
        'description',
        'permissions',
        'actif',
    ];

    protected $casts = [
        'permissions' => 'array',
        'actif' => 'boolean',
    ];

    // Relations
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function usersWithRole(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Méthodes utilitaires
    public function hasPermission($permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    // Méthodes statiques pour créer les rôles par défaut
    public static function createDefaultRoles()
    {
        $roles = [
            [
                'nom' => 'admin_general',
                'libelle' => 'Administrateur Général',
                'description' => 'Accès complet à tous les niveaux, peut créer piliers et objectifs stratégiques',
                'permissions' => ['all'],
            ],
            [
                'nom' => 'owner_os',
                'libelle' => 'Owner Objectif Stratégique',
                'description' => 'Peut créer des objectifs spécifiques et voir les niveaux inférieurs',
                'permissions' => ['create_objectif_specifique', 'view_own_levels'],
            ],
            [
                'nom' => 'owner_pil',
                'libelle' => 'Owner Pilotage',
                'description' => 'Peut créer des actions et voir les niveaux inférieurs',
                'permissions' => ['create_action', 'view_own_levels'],
            ],
            [
                'nom' => 'owner_action',
                'libelle' => 'Owner Action',
                'description' => 'Peut créer des sous-actions et voir les niveaux inférieurs',
                'permissions' => ['create_sous_action', 'view_own_levels'],
            ],
            [
                'nom' => 'owner_sa',
                'libelle' => 'Owner Sous-Action',
                'description' => 'Peut uniquement voir et modifier ses sous-actions',
                'permissions' => ['view_own_sa', 'update_own_sa'],
            ],
        ];

        foreach ($roles as $roleData) {
            self::firstOrCreate(['nom' => $roleData['nom']], $roleData);
        }
    }
} 