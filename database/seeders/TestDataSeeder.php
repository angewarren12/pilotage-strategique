<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pilier;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\Action;
use App\Models\SousAction;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des utilisateurs de test avec différents rôles
        $adminGeneral = User::where('email', 'admin@pilotage-strategique.com')->first();
        
        $ownerOS = User::firstOrCreate(
            ['email' => 'marie.dubois@entreprise.com'],
            [
                'name' => 'Marie Dubois',
                'password' => bcrypt('password123'),
                'role_id' => Role::where('nom', 'owner_os')->first()->id,
            ]
        );

        $ownerPIL = User::firstOrCreate(
            ['email' => 'jean.martin@entreprise.com'],
            [
                'name' => 'Jean Martin',
                'password' => bcrypt('password123'),
                'role_id' => Role::where('nom', 'owner_pil')->first()->id,
            ]
        );

        $ownerAction = User::firstOrCreate(
            ['email' => 'sophie.bernard@entreprise.com'],
            [
                'name' => 'Sophie Bernard',
                'password' => bcrypt('password123'),
                'role_id' => Role::where('nom', 'owner_action')->first()->id,
            ]
        );

        $ownerSA = User::firstOrCreate(
            ['email' => 'pierre.durand@entreprise.com'],
            [
                'name' => 'Pierre Durand',
                'password' => bcrypt('password123'),
                'role_id' => Role::where('nom', 'owner_sa')->first()->id,
            ]
        );

        // Vérifier si les piliers existent déjà
        if (Pilier::count() == 0) {
            // Créer des piliers
            $pilier1 = Pilier::create([
                'code' => 'P1',
                'libelle' => 'Développement Durable',
                'description' => 'Pilier axé sur la responsabilité environnementale et sociale',
                'owner_id' => $adminGeneral->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            $pilier2 = Pilier::create([
                'code' => 'P2',
                'libelle' => 'Innovation Technologique',
                'description' => 'Pilier dédié à l\'innovation et à la transformation digitale',
                'owner_id' => $adminGeneral->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            $pilier3 = Pilier::create([
                'code' => 'P3',
                'libelle' => 'Excellence Opérationnelle',
                'description' => 'Pilier pour l\'optimisation des processus et la qualité',
                'owner_id' => $adminGeneral->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            // Créer des objectifs stratégiques
            $os1 = ObjectifStrategique::create([
                'code' => 'OS1',
                'libelle' => 'Réduire l\'empreinte carbone de 30%',
                'description' => 'Objectif de réduction des émissions de CO2',
                'pilier_id' => $pilier1->id,
                'owner_id' => $ownerOS->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            $os2 = ObjectifStrategique::create([
                'code' => 'OS2',
                'libelle' => 'Digitaliser 80% des processus',
                'description' => 'Transformation digitale des opérations',
                'pilier_id' => $pilier2->id,
                'owner_id' => $ownerOS->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            $os3 = ObjectifStrategique::create([
                'code' => 'OS3',
                'libelle' => 'Améliorer la satisfaction client de 25%',
                'description' => 'Optimisation de l\'expérience client',
                'pilier_id' => $pilier3->id,
                'owner_id' => $ownerOS->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            // Créer des objectifs spécifiques
            $objSpec1 = ObjectifSpecifique::create([
                'code' => 'PIL1',
                'libelle' => 'Mise en place des énergies renouvelables',
                'description' => 'Installation de panneaux solaires et éoliennes',
                'objectif_strategique_id' => $os1->id,
                'owner_id' => $ownerPIL->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            $objSpec2 = ObjectifSpecifique::create([
                'code' => 'PIL2',
                'libelle' => 'Optimisation de la logistique',
                'description' => 'Réduction des transports et optimisation des routes',
                'objectif_strategique_id' => $os1->id,
                'owner_id' => $ownerPIL->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            $objSpec3 = ObjectifSpecifique::create([
                'code' => 'PIL3',
                'libelle' => 'Migration vers le cloud',
                'description' => 'Transfert des infrastructures vers le cloud',
                'objectif_strategique_id' => $os2->id,
                'owner_id' => $ownerPIL->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            $objSpec4 = ObjectifSpecifique::create([
                'code' => 'PIL4',
                'libelle' => 'Formation des équipes',
                'description' => 'Programme de formation aux nouvelles technologies',
                'objectif_strategique_id' => $os2->id,
                'owner_id' => $ownerPIL->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            $objSpec5 = ObjectifSpecifique::create([
                'code' => 'PIL5',
                'libelle' => 'Amélioration du service client',
                'description' => 'Mise en place d\'un centre de support 24/7',
                'objectif_strategique_id' => $os3->id,
                'owner_id' => $ownerPIL->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            // Créer des actions
            $action1 = Action::create([
                'code' => 'A1',
                'libelle' => 'Étude de faisabilité énergétique',
                'description' => 'Analyse technique et économique des solutions',
                'objectif_specifique_id' => $objSpec1->id,
                'owner_id' => $ownerAction->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            $action2 = Action::create([
                'code' => 'A2',
                'libelle' => 'Installation des panneaux solaires',
                'description' => 'Mise en place de l\'infrastructure solaire',
                'objectif_specifique_id' => $objSpec1->id,
                'owner_id' => $ownerAction->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            $action3 = Action::create([
                'code' => 'A3',
                'libelle' => 'Audit logistique',
                'description' => 'Analyse des flux de transport actuels',
                'objectif_specifique_id' => $objSpec2->id,
                'owner_id' => $ownerAction->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            $action4 = Action::create([
                'code' => 'A4',
                'libelle' => 'Migration des serveurs',
                'description' => 'Transfert des données vers AWS',
                'objectif_specifique_id' => $objSpec3->id,
                'owner_id' => $ownerAction->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            $action5 = Action::create([
                'code' => 'A5',
                'libelle' => 'Création des modules de formation',
                'description' => 'Développement du contenu pédagogique',
                'objectif_specifique_id' => $objSpec4->id,
                'owner_id' => $ownerAction->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            $action6 = Action::create([
                'code' => 'A6',
                'libelle' => 'Recrutement des agents support',
                'description' => 'Élargissement de l\'équipe support client',
                'objectif_specifique_id' => $objSpec5->id,
                'owner_id' => $ownerAction->id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            // Créer des sous-actions avec différents taux d'avancement
            $sousActions = [
                // Action 1 - Étude de faisabilité
                [
                    'code' => 'SA1',
                    'libelle' => 'Analyse des besoins énergétiques',
                    'description' => 'Évaluation de la consommation actuelle',
                    'action_id' => $action1->id,
                    'owner_id' => $ownerSA->id,
                    'taux_avancement' => 100,
                    'date_echeance' => Carbon::now()->subDays(5),
                    'date_realisation' => Carbon::now()->subDays(3),
                    'statut' => 'termine',
                    'actif' => true,
                ],
                [
                    'code' => 'SA2',
                    'libelle' => 'Étude des fournisseurs',
                    'description' => 'Comparaison des offres du marché',
                    'action_id' => $action1->id,
                    'owner_id' => $ownerSA->id,
                    'taux_avancement' => 75,
                    'date_echeance' => Carbon::now()->addDays(10),
                    'statut' => 'en_cours',
                    'actif' => true,
                ],
                [
                    'code' => 'SA3',
                    'libelle' => 'Rapport de recommandation',
                    'description' => 'Rédaction du rapport final',
                    'action_id' => $action1->id,
                    'owner_id' => $ownerSA->id,
                    'taux_avancement' => 25,
                    'date_echeance' => Carbon::now()->addDays(20),
                    'statut' => 'en_cours',
                    'actif' => true,
                ],

                // Action 2 - Installation panneaux
                [
                    'code' => 'SA4',
                    'libelle' => 'Préparation du site',
                    'description' => 'Aménagement de l\'espace d\'installation',
                    'action_id' => $action2->id,
                    'owner_id' => $ownerSA->id,
                    'taux_avancement' => 50,
                    'date_echeance' => Carbon::now()->addDays(15),
                    'statut' => 'en_cours',
                    'actif' => true,
                ],
                [
                    'code' => 'SA5',
                    'libelle' => 'Commande des équipements',
                    'description' => 'Achat des panneaux et accessoires',
                    'action_id' => $action2->id,
                    'owner_id' => $ownerSA->id,
                    'taux_avancement' => 0,
                    'date_echeance' => Carbon::now()->addDays(30),
                    'statut' => 'en_cours',
                    'actif' => true,
                ],

                // Action 3 - Audit logistique
                [
                    'code' => 'SA6',
                    'libelle' => 'Collecte des données de transport',
                    'description' => 'Gathering des informations sur les flux',
                    'action_id' => $action3->id,
                    'owner_id' => $ownerSA->id,
                    'taux_avancement' => 90,
                    'date_echeance' => Carbon::now()->subDays(2),
                    'statut' => 'en_retard',
                    'actif' => true,
                ],
                [
                    'code' => 'SA7',
                    'libelle' => 'Analyse des données',
                    'description' => 'Traitement et analyse des informations',
                    'action_id' => $action3->id,
                    'owner_id' => $ownerSA->id,
                    'taux_avancement' => 60,
                    'date_echeance' => Carbon::now()->addDays(8),
                    'statut' => 'en_cours',
                    'actif' => true,
                ],

                // Action 4 - Migration serveurs
                [
                    'code' => 'SA8',
                    'libelle' => 'Sauvegarde des données',
                    'description' => 'Backup complet avant migration',
                    'action_id' => $action4->id,
                    'owner_id' => $ownerSA->id,
                    'taux_avancement' => 100,
                    'date_echeance' => Carbon::now()->subDays(1),
                    'date_realisation' => Carbon::now()->subDays(1),
                    'statut' => 'termine',
                    'actif' => true,
                ],
                [
                    'code' => 'SA9',
                    'libelle' => 'Configuration AWS',
                    'description' => 'Mise en place de l\'infrastructure cloud',
                    'action_id' => $action4->id,
                    'owner_id' => $ownerSA->id,
                    'taux_avancement' => 40,
                    'date_echeance' => Carbon::now()->addDays(12),
                    'statut' => 'en_cours',
                    'actif' => true,
                ],

                // Action 5 - Modules de formation
                [
                    'code' => 'SA10',
                    'libelle' => 'Définition du programme',
                    'description' => 'Élaboration du contenu pédagogique',
                    'action_id' => $action5->id,
                    'owner_id' => $ownerSA->id,
                    'taux_avancement' => 80,
                    'date_echeance' => Carbon::now()->addDays(5),
                    'statut' => 'en_cours',
                    'actif' => true,
                ],
                [
                    'code' => 'SA11',
                    'libelle' => 'Création des supports',
                    'description' => 'Développement des documents de formation',
                    'action_id' => $action5->id,
                    'owner_id' => $ownerSA->id,
                    'taux_avancement' => 30,
                    'date_echeance' => Carbon::now()->addDays(18),
                    'statut' => 'en_cours',
                    'actif' => true,
                ],

                // Action 6 - Recrutement support
                [
                    'code' => 'SA12',
                    'libelle' => 'Rédaction des offres d\'emploi',
                    'description' => 'Création des annonces de recrutement',
                    'action_id' => $action6->id,
                    'owner_id' => $ownerSA->id,
                    'taux_avancement' => 100,
                    'date_echeance' => Carbon::now()->subDays(3),
                    'date_realisation' => Carbon::now()->subDays(2),
                    'statut' => 'termine',
                    'actif' => true,
                ],
                [
                    'code' => 'SA13',
                    'libelle' => 'Sélection des candidats',
                    'description' => 'Entretiens et évaluation des profils',
                    'action_id' => $action6->id,
                    'owner_id' => $ownerSA->id,
                    'taux_avancement' => 70,
                    'date_echeance' => Carbon::now()->addDays(7),
                    'statut' => 'en_cours',
                    'actif' => true,
                ],
            ];

            foreach ($sousActions as $sousActionData) {
                SousAction::create($sousActionData);
            }

            $this->command->info('Données de test créées avec succès !');
        } else {
            $this->command->info('Les données de test existent déjà !');
        }

        $this->command->info('Utilisateurs disponibles :');
        $this->command->info('- Admin Général: admin@pilotage-strategique.com');
        $this->command->info('- Owner OS: marie.dubois@entreprise.com');
        $this->command->info('- Owner PIL: jean.martin@entreprise.com');
        $this->command->info('- Owner Action: sophie.bernard@entreprise.com');
        $this->command->info('- Owner SA: pierre.durand@entreprise.com');
        $this->command->info('Mot de passe pour tous : password123');
    }
}
