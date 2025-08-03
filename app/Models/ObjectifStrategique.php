<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectifStrategique extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'pilier_id',
        'owner_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pilier()
    {
        return $this->belongsTo(Pilier::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function objectifsSpecifiques()
    {
        return $this->hasMany(ObjectifSpecifique::class);
    }

    /**
     * Calcule le taux d'avancement en temps réel basé sur les objectifs spécifiques
     */
    public function getTauxAvancementAttribute()
    {
        $objectifsSpecifiques = $this->objectifsSpecifiques;
        
        if ($objectifsSpecifiques->isEmpty()) {
            return 0;
        }

        $totalTaux = 0;
        $count = 0;

        foreach ($objectifsSpecifiques as $objectifSpecifique) {
            $taux = $objectifSpecifique->taux_avancement;
            if ($taux !== null) {
                $totalTaux += $taux;
                $count++;
            }
        }

        return $count > 0 ? round($totalTaux / $count, 2) : 0;
    }
}
