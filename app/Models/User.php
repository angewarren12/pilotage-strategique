<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relations
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    // Relations pour les éléments dont l'utilisateur est owner
    public function piliers(): HasMany
    {
        return $this->hasMany(Pilier::class, 'owner_id');
    }

    public function objectifsStrategiques(): HasMany
    {
        return $this->hasMany(ObjectifStrategique::class, 'owner_id');
    }

    public function objectifsSpecifiques(): HasMany
    {
        return $this->hasMany(ObjectifSpecifique::class, 'owner_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class, 'owner_id');
    }

    public function sousActions(): HasMany
    {
        return $this->hasMany(SousAction::class, 'owner_id');
    }

    // Méthodes de vérification des rôles
    public function hasRole($roleName): bool
    {
        return $this->role && $this->role->nom === $roleName;
    }

    public function isAdminGeneral(): bool
    {
        return $this->hasRole('admin_general');
    }

    public function isOwnerOS(): bool
    {
        return $this->hasRole('owner_os');
    }

    public function isOwnerPIL(): bool
    {
        return $this->hasRole('owner_pil');
    }

    public function isOwnerAction(): bool
    {
        return $this->hasRole('owner_action');
    }

    public function isOwnerSA(): bool
    {
        return $this->hasRole('owner_sa');
    }

    // Méthodes de permissions
    public function canCreatePilier(): bool
    {
        return $this->isAdminGeneral();
    }

    public function canCreateObjectifStrategique(): bool
    {
        return $this->isAdminGeneral();
    }

    public function canCreateObjectifSpecifique(): bool
    {
        return $this->isOwnerOS();
    }

    public function canCreateAction(): bool
    {
        return $this->isOwnerPIL();
    }

    public function canCreateSousAction(): bool
    {
        return $this->isOwnerAction();
    }

    public function canUpdateSousAction(): bool
    {
        return $this->isOwnerSA() || $this->isOwnerAction() || $this->isOwnerPIL() || $this->isOwnerOS() || $this->isAdminGeneral();
    }

    public function canUpdatePilier(): bool
    {
        return $this->isAdminGeneral();
    }

    public function canUpdateObjectifStrategique(): bool
    {
        return $this->isAdminGeneral();
    }

    public function canUpdateObjectifSpecifique(): bool
    {
        return $this->isOwnerOS() || $this->isAdminGeneral();
    }

    public function canUpdateAction(): bool
    {
        return $this->isOwnerPIL() || $this->isOwnerOS() || $this->isAdminGeneral();
    }

    // Nouvelles méthodes de permissions pour les objectifs stratégiques
    public function canEditObjectifStrategique(ObjectifStrategique $objectifStrategique): bool
    {
        return $this->isAdminGeneral() || $this->id === $objectifStrategique->owner_id;
    }

    public function canDeleteObjectifStrategique(ObjectifStrategique $objectifStrategique): bool
    {
        return $this->isAdminGeneral() || $this->id === $objectifStrategique->owner_id;
    }
}
