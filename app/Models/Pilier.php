<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Pilier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'taux_avancement',
        'actif'
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
            // Utiliser le calcul en temps réel de l'objectif stratégique
            $taux = $objectifStrategique->getCalculatedTauxAvancement();
            if ($taux !== null) {
                $totalTaux += $taux;
                $count++;
            }
        }

        return $count > 0 ? round($totalTaux / $count, 2) : 0;
    }

    /**
     * Méthode pour obtenir le taux d'avancement calculé sans récursion
     */
    public function getCalculatedTauxAvancement()
    {
        return $this->getTauxAvancementAttribute();
    }

    /**
     * Met à jour le taux d'avancement du pilier basé sur ses objectifs stratégiques
     * Cette méthode est appelée quand les taux d'avancement des objectifs stratégiques changent
     */
    public function updateTauxAvancement(): void
    {
        // Le taux d'avancement est calculé automatiquement via l'accesseur
        // Cette méthode peut être utilisée pour forcer un recalcul ou des actions supplémentaires
        
        // Optionnel : déclencher des événements ou notifications
        $this->touch(); // Force la mise à jour du timestamp updated_at
        
        // Optionnel : logger le changement
        Log::info('Taux d\'avancement du pilier mis à jour', [
            'pilier_id' => $this->id,
            'pilier_code' => $this->code,
            'nouveau_taux' => $this->getTauxAvancementAttribute()
        ]);
    }

    /**
     * Génère une variation de couleur pour un niveau hiérarchique donné
     * @param int $level Niveau hiérarchique (0 = pilier, 1 = OS, 2 = OSP, 3 = Action, 4 = SA)
     * @return string Couleur hexadécimale
     */
    public function getHierarchicalColor($level = 0)
    {
        $baseColor = $this->color ?? '#007bff';
        
        // Convertir la couleur hex en RGB
        $hex = ltrim($baseColor, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Facteurs de variation selon le niveau
        $factors = [
            0 => 1.0,    // Pilier - couleur pure
            1 => 0.85,    // OS - légèrement plus clair
            2 => 0.7,     // OSP - plus clair
            3 => 0.55,    // Action - encore plus clair
            4 => 0.4      // SA - très clair
        ];
        
        $factor = $factors[$level] ?? 1.0;
        
        // Appliquer la variation
        $r = min(255, round($r + (255 - $r) * (1 - $factor)));
        $g = min(255, round($g + (255 - $g) * (1 - $factor)));
        $b = min(255, round($b + (255 - $b) * (1 - $factor)));
        
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Génère un gradient CSS basé sur la couleur du pilier
     * @param int $level Niveau hiérarchique
     * @return string CSS gradient
     */
    public function getHierarchicalGradient($level = 0)
    {
        $baseColor = $this->getHierarchicalColor($level);
        $lighterColor = $this->getHierarchicalColor($level + 1);
        
        return "linear-gradient(135deg, {$baseColor} 0%, {$lighterColor} 100%)";
    }

    /**
     * Génère une couleur de texte appropriée pour la couleur de fond
     * @param string $backgroundColor Couleur de fond hexadécimale
     * @return string 'white' ou 'dark'
     */
    public function getTextColor($backgroundColor = null)
    {
        $color = $backgroundColor ?? $this->color ?? '#007bff';
        $hex = ltrim($color, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Calculer la luminosité
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        return $luminance > 0.5 ? 'dark' : 'white';
    }

    // Événements
    protected static function booted()
    {
        static::saved(function ($pilier) {
            // Vérifier si le taux d'avancement a changé (calculé automatiquement)
            $currentTaux = $pilier->getTauxAvancementAttribute();
            $oldTaux = $pilier->getOriginal('taux_avancement') ?? 0;
            
            if (abs($currentTaux - $oldTaux) > 0.01) { // Tolérance de 0.01%
                // Créer une notification de changement d'avancement
                app(\App\Services\NotificationService::class)->notifyAvancementChange(
                    'pilier',
                    $pilier->id,
                    $oldTaux,
                    $currentTaux,
                    $pilier
                );
            }
        });
    }
}
