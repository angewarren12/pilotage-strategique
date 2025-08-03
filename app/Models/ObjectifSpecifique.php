<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectifSpecifique extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'objectif_strategique_id',
        'owner_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function objectifStrategique()
    {
        return $this->belongsTo(ObjectifStrategique::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    /**
     * Calcule le taux d'avancement en temps réel basé sur les actions
     */
    public function getTauxAvancementAttribute()
    {
        $actions = $this->actions;
        
        if ($actions->isEmpty()) {
            return 0;
        }

        $totalTaux = 0;
        $count = 0;

        foreach ($actions as $action) {
            $taux = $action->taux_avancement;
            if ($taux !== null) {
                $totalTaux += $taux;
                $count++;
            }
        }

        return $count > 0 ? round($totalTaux / $count, 2) : 0;
    }
}
