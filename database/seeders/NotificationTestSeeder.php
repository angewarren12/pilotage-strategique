<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Pilier;
use App\Services\NotificationService;

class NotificationTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Création de notifications de test...');
        
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
        
        // Récupérer un pilier
        $pilier = Pilier::first();
        if (!$pilier) {
            $this->command->error('Aucun pilier trouvé. Créez d\'abord des piliers.');
            return;
        }
        
        // Créer des notifications de test
        $notifications = [
            [
                'user_id' => $admin->id,
                'type' => 'avancement_change',
                'title' => 'Changement d\'avancement',
                'message' => 'L\'avancement du pilier P1 a changé de 0% à 25%',
                'data' => [
                    'element_type' => 'pilier',
                    'element_id' => $pilier->id,
                    'old_value' => 0,
                    'new_value' => 25
                ],
                'priority' => 'normal'
            ],
            [
                'user_id' => $admin->id,
                'type' => 'echeance_approche',
                'title' => 'Échéance approche',
                'message' => 'L\'échéance de la sous-action SA1 approche (dans 3 jours)',
                'data' => [
                    'element_type' => 'sous_action',
                    'element_id' => 1,
                    'days_left' => 3
                ],
                'priority' => 'high'
            ],
            [
                'user_id' => $admin->id,
                'type' => 'comment_new',
                'title' => 'Nouveau commentaire',
                'message' => 'John Doe a ajouté un commentaire sur l\'action ACT1',
                'data' => [
                    'element_type' => 'action',
                    'element_id' => 1,
                    'comment_author' => 'John Doe'
                ],
                'priority' => 'normal'
            ],
            [
                'user_id' => $admin->id,
                'type' => 'delai_depasse',
                'title' => 'Délai dépassé',
                'message' => 'Le délai de la sous-action SA2 a été dépassé de 5 jours',
                'data' => [
                    'element_type' => 'sous_action',
                    'element_id' => 2,
                    'days_late' => 5
                ],
                'priority' => 'urgent'
            ]
        ];
        
        foreach ($notifications as $notificationData) {
            Notification::create($notificationData);
        }
        
        $this->command->info('Notifications de test créées avec succès !');
    }
}
