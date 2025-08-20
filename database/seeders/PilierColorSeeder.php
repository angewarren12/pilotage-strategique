<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pilier;

class PilierColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Couleurs disponibles pour les piliers
        $colors = [
            '#007bff', // Bleu
            '#28a745', // Vert
            '#dc3545', // Rouge
            '#ffc107', // Jaune
            '#6f42c1', // Violet
            '#fd7e14', // Orange
            '#20c997', // Turquoise
            '#e83e8c', // Rose
        ];

        // Mettre à jour tous les piliers existants avec des couleurs
        $piliers = Pilier::all();
        $colorIndex = 0;

        foreach ($piliers as $pilier) {
            // Si le pilier n'a pas de couleur, lui en assigner une
            if (!$pilier->color) {
                $pilier->update([
                    'color' => $colors[$colorIndex % count($colors)]
                ]);
                $colorIndex++;
            }
        }

        $this->command->info('Couleurs assignées aux piliers avec succès !');
    }
}
