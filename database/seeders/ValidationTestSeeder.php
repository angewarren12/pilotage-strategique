<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Validation;
use App\Models\User;
use App\Models\Pilier;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\Action;
use App\Models\SousAction;
use App\Services\ValidationService;
use Carbon\Carbon;

class ValidationTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Création de validations de test...');
        
        // Récupérer un utilisateur admin
        $admin = User::whereHas('role', function($query) {
            $query->where('nom', 'admin_general');
        })->first();
        
        if (!$admin) {
            // Si aucun admin trouvé, prendre le premier utilisateur
            $admin = User::first();
            if (!$admin) {
                $this->command->error('Aucun utilisateur trouvé. Créez d\'abord des utilisateurs.');
                return;
            }
        }
        
        // Récupérer des éléments pour tester
        $pilier = Pilier::first();
        $objectifStrategique = ObjectifStrategique::first();
        $objectifSpecifique = ObjectifSpecifique::first();
        $action = Action::first();
        $sousAction = SousAction::first();
        
        if (!$pilier) {
            $this->command->error('Aucun pilier trouvé. Créez d\'abord des piliers.');
            return;
        }
        
        // Créer des validations de test
        $validations = [
            [
                'element_type' => 'pilier',
                'element_id' => $pilier->id,
                'requested_by' => $admin->id,
                'status' => 'pending',
                'comments' => 'Demande de validation pour changement de propriétaire',
                'validation_data' => [
                    'action' => 'change_owner',
                    'old_owner_id' => $pilier->owner_id,
                    'new_owner_id' => $admin->id,
                    'reason' => 'Réorganisation des responsabilités'
                ],
                'expires_at' => Carbon::now()->addDays(7)
            ],
            [
                'element_type' => 'objectif_strategique',
                'element_id' => $objectifStrategique ? $objectifStrategique->id : 1,
                'requested_by' => $admin->id,
                'status' => 'pending',
                'comments' => 'Demande de validation pour modification d\'échéance',
                'validation_data' => [
                    'action' => 'change_deadline',
                    'old_deadline' => '2024-12-31',
                    'new_deadline' => '2025-06-30',
                    'reason' => 'Extension nécessaire pour compléter les objectifs'
                ],
                'expires_at' => Carbon::now()->addDays(5)
            ],
            [
                'element_type' => 'action',
                'element_id' => $action ? $action->id : 1,
                'requested_by' => $admin->id,
                'status' => 'approved',
                'validated_by' => $admin->id,
                'validated_at' => Carbon::now()->subDays(2),
                'comments' => 'Validation approuvée pour changement de statut',
                'validation_data' => [
                    'action' => 'change_status',
                    'old_status' => 'en_cours',
                    'new_status' => 'termine',
                    'reason' => 'Action terminée avec succès'
                ],
                'expires_at' => Carbon::now()->addDays(7)
            ],
            [
                'element_type' => 'sous_action',
                'element_id' => $sousAction ? $sousAction->id : 1,
                'requested_by' => $admin->id,
                'status' => 'rejected',
                'validated_by' => $admin->id,
                'validated_at' => Carbon::now()->subDays(1),
                'comments' => 'Validation rejetée',
                'rejection_reason' => 'Données insuffisantes pour justifier le changement',
                'validation_data' => [
                    'action' => 'change_budget',
                    'old_budget' => 10000,
                    'new_budget' => 15000,
                    'reason' => 'Augmentation nécessaire pour couvrir les coûts'
                ],
                'expires_at' => Carbon::now()->addDays(7)
            ]
        ];
        
        foreach ($validations as $validationData) {
            // Vérifier si l'élément existe avant de créer la validation
            $elementExists = false;
            switch ($validationData['element_type']) {
                case 'pilier':
                    $elementExists = Pilier::find($validationData['element_id']) !== null;
                    break;
                case 'objectif_strategique':
                    $elementExists = ObjectifStrategique::find($validationData['element_id']) !== null;
                    break;
                case 'objectif_specifique':
                    $elementExists = ObjectifSpecifique::find($validationData['element_id']) !== null;
                    break;
                case 'action':
                    $elementExists = Action::find($validationData['element_id']) !== null;
                    break;
                case 'sous_action':
                    $elementExists = SousAction::find($validationData['element_id']) !== null;
                    break;
            }
            
            if ($elementExists) {
                Validation::create($validationData);
                $this->command->line("Validation créée pour {$validationData['element_type']} ID {$validationData['element_id']}");
            } else {
                $this->command->warn("Élément {$validationData['element_type']} ID {$validationData['element_id']} non trouvé, validation ignorée");
            }
        }
        
        $this->command->info('Validations de test créées avec succès !');
    }
}
