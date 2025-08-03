<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pilier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'owner_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function objectifsStrategiques()
    {
        return $this->hasMany(ObjectifStrategique::class);
    }

    /**
     * Calcule le taux d'avancement en temps réel basé sur les objectifs stratégiques
     */
    public function getTauxAvancementAttribute()
    {
        $objectifsStrategiques = $this->objectifsStrategiques;
        
        if ($objectifsStrategiques->isEmpty()) {
            return 0;
        }

        $totalTaux = 0;
        $count = 0;

        foreach ($objectifsStrategiques as $objectifStrategique) {
            $taux = $objectifStrategique->taux_avancement;
            if ($taux !== null) {
                $totalTaux += $taux;
                $count++;
            }
        }

        return $count > 0 ? round($totalTaux / $count, 2) : 0;
    }
}
