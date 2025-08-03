<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Pilier;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\Action;
use App\Models\SousAction;
use Carbon\Carbon;

class ExcelDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer les rôles
        $this->createRoles();
        
        // 2. Créer les utilisateurs
        $this->createUsers();
        
        // 3. Créer la structure hiérarchique
        $this->createHierarchicalStructure();
        
        $this->command->info('Données Excel importées avec succès !');
    }

    private function createRoles()
    {
        Role::createDefaultRoles();
        $this->command->info('Rôles créés');
    }

    private function createUsers()
    {
        $users = [
            [
                'name' => 'Pdt Copil',
                'email' => 'pdt.copil@entreprise.com',
                'password' => bcrypt('password123'),
                'role_nom' => 'admin_general'
            ],
            [
                'name' => 'DGAS',
                'email' => 'dgas@entreprise.com',
                'password' => bcrypt('password123'),
                'role_nom' => 'owner_os'
            ],
            [
                'name' => 'DGAO',
                'email' => 'dgao@entreprise.com',
                'password' => bcrypt('password123'),
                'role_nom' => 'owner_os'
            ],
            [
                'name' => 'DG MACI',
                'email' => 'dg.maci@entreprise.com',
                'password' => bcrypt('password123'),
                'role_nom' => 'owner_pil'
            ],
            [
                'name' => 'DG SICTA',
                'email' => 'dg.sicta@entreprise.com',
                'password' => bcrypt('password123'),
                'role_nom' => 'owner_pil'
            ],
            [
                'name' => 'DG MAGUI',
                'email' => 'dg.magui@entreprise.com',
                'password' => bcrypt('password123'),
                'role_nom' => 'owner_pil'
            ]
        ];

        foreach ($users as $userData) {
            $role = Role::where('nom', $userData['role_nom'])->first();
            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'role_id' => $role->id
            ]);
        }
        
        $this->command->info('Utilisateurs créés');
    }

    private function createHierarchicalStructure()
    {
        // Créer le Pilier P1
        $pilier1 = Pilier::create([
            'code' => 'P1',
            'libelle' => 'Croissance des Activités et du Chiffre d\'Affaires',
            'description' => 'Pilier stratégique pour la croissance des activités',
            'owner_id' => User::where('name', 'Pdt Copil')->first()->id,
            'taux_avancement' => 46,
            'actif' => true
        ]);

        // Créer l'Objectif Stratégique P1.OS1
        $objectifStrategique1 = ObjectifStrategique::create([
            'code' => 'OS1',
            'libelle' => 'Croissance des Activités et du Chiffre d\'Affaires',
            'description' => 'Objectif stratégique de croissance',
            'pilier_id' => $pilier1->id,
            'owner_id' => User::where('name', 'Pdt Copil')->first()->id,
            'taux_avancement' => 46,
            'actif' => true
        ]);

        // Créer les Objectifs Spécifiques (PIL)
        $this->createObjectifsSpecifiques($objectifStrategique1);
    }

    private function createObjectifsSpecifiques($objectifStrategique)
    {
        // PIL1: Acquisition SICTA
        $pil1 = ObjectifSpecifique::create([
            'code' => 'PIL1',
            'libelle' => 'Acquisition SICTA',
            'description' => 'Acquisition de SICTA',
            'objectif_strategique_id' => $objectifStrategique->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 91,
            'actif' => true
        ]);

        // PIL2: Ouverture de nouvelles stations
        $pil2 = ObjectifSpecifique::create([
            'code' => 'PIL2',
            'libelle' => 'Ouverture de nouvelles stations pour couverture optimale du territoire',
            'description' => 'Ouverture de nouvelles stations',
            'objectif_strategique_id' => $objectifStrategique->id,
            'owner_id' => User::where('name', 'DGAO')->first()->id,
            'taux_avancement' => 47,
            'actif' => true
        ]);

        // PIL3: Augmenter le taux de pénétration
        $pil3 = ObjectifSpecifique::create([
            'code' => 'PIL3',
            'libelle' => 'Augmenter le taux de pénétration du marché vers les véhicules réfractaires',
            'description' => 'Augmentation du taux de pénétration',
            'objectif_strategique_id' => $objectifStrategique->id,
            'owner_id' => User::where('name', 'DGAO')->first()->id,
            'taux_avancement' => 32,
            'actif' => true
        ]);

        // PIL4: Identifier de nouvelles activités
        $pil4 = ObjectifSpecifique::create([
            'code' => 'PIL4',
            'libelle' => 'Identifier de nouvelles activités pour diversification',
            'description' => 'Identification de nouvelles activités',
            'objectif_strategique_id' => $objectifStrategique->id,
            'owner_id' => User::where('name', 'DGAO')->first()->id,
            'taux_avancement' => 13,
            'actif' => true
        ]);

        // Créer les actions pour chaque PIL
        $this->createActionsForPIL1($pil1);
        $this->createActionsForPIL2($pil2);
        $this->createActionsForPIL3($pil3);
        $this->createActionsForPIL4($pil4);
    }

    private function createActionsForPIL1($pil1)
    {
        // A1: Signature du protocole d'accord de cession
        $action1 = Action::create([
            'code' => 'A1',
            'libelle' => 'Signature du protocole d\'accord de cession',
            'description' => 'Signature du protocole d\'accord de cession',
            'objectif_specifique_id' => $pil1->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 100,
            'actif' => true
        ]);

        // A2: Finaliser levée de fonds
        $action2 = Action::create([
            'code' => 'A2',
            'libelle' => 'Finaliser levée de fonds',
            'description' => 'Finalisation de la levée de fonds',
            'objectif_specifique_id' => $pil1->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 81,
            'actif' => true
        ]);

        // A3: Finaliser augmentation de capital CNPS
        $action3 = Action::create([
            'code' => 'A3',
            'libelle' => 'Finaliser augmentation de capital CNPS',
            'description' => 'Finalisation de l\'augmentation de capital CNPS',
            'objectif_specifique_id' => $pil1->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 83,
            'actif' => true
        ]);

        // A4: Signature des actes de cessions 5% M. Georges N'DIA
        $action4 = Action::create([
            'code' => 'A4',
            'libelle' => 'Signature des actes de cessions 5% M. Georges N\'DIA',
            'description' => 'Signature des actes de cessions',
            'objectif_specifique_id' => $pil1->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 100,
            'actif' => true
        ]);

        // Créer les sous-actions
        $this->createSousActionsForA1($action1);
        $this->createSousActionsForA2($action2);
        $this->createSousActionsForA3($action3);
        $this->createSousActionsForA4($action4);
    }

    private function createSousActionsForA1($action1)
    {
        // SA1: Finalisation bouclage et preuve du financement
        SousAction::create([
            'code' => 'SA1',
            'libelle' => 'Finalisation bouclage et preuve du financement',
            'description' => 'Finalisation du bouclage et preuve du financement',
            'action_id' => $action1->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2025-01-01',
            'date_realisation' => '2025-03-30',
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);

        // SA2: Signature SPA
        SousAction::create([
            'code' => 'SA2',
            'libelle' => 'Signature SPA',
            'description' => 'Signature du SPA',
            'action_id' => $action1->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2025-01-30',
            'date_realisation' => '2025-04-01',
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);
    }

    private function createSousActionsForA2($action2)
    {
        // SA1: 5,7 mds BNI
        SousAction::create([
            'code' => 'SA1',
            'libelle' => '5,7 mds BNI',
            'description' => 'Levée de fonds BNI',
            'action_id' => $action2->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 75,
            'date_echeance' => '2025-03-15',
            'date_realisation' => null,
            'ecart_jours' => 135,
            'statut' => 'en_retard',
            'actif' => true
        ]);

        // SA2: 15 moi EUR AFRASIA
        SousAction::create([
            'code' => 'SA2',
            'libelle' => '15 moi EUR AFRASIA',
            'description' => 'Levée de fonds AFRASIA',
            'action_id' => $action2->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 75,
            'date_echeance' => '2025-03-15',
            'date_realisation' => null,
            'ecart_jours' => 135,
            'statut' => 'en_retard',
            'actif' => true
        ]);

        // SA3: 5,7 mds BACI
        SousAction::create([
            'code' => 'SA3',
            'libelle' => '5,7 mds BACI',
            'description' => 'Levée de fonds BACI',
            'action_id' => $action2->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 75,
            'date_echeance' => '2025-03-15',
            'date_realisation' => null,
            'ecart_jours' => 135,
            'statut' => 'en_retard',
            'actif' => true
        ]);

        // SA4: 10,5 mds BDA
        SousAction::create([
            'code' => 'SA4',
            'libelle' => '10,5 mds BDA',
            'description' => 'Levée de fonds BDA',
            'action_id' => $action2->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2025-03-15',
            'date_realisation' => null,
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);
    }

    private function createSousActionsForA3($action3)
    {
        // SA1: Finalisation et signature des actes
        SousAction::create([
            'code' => 'SA1',
            'libelle' => 'Finalisation et signature des actes',
            'description' => 'Finalisation et signature des actes CNPS',
            'action_id' => $action3->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2025-03-07',
            'date_realisation' => null,
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);

        // SA2: Décaissement CNPS et transfert des fonds
        SousAction::create([
            'code' => 'SA2',
            'libelle' => 'Décaissement CNPS et transfert des fonds',
            'description' => 'Décaissement CNPS et transfert des fonds',
            'action_id' => $action3->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 50,
            'date_echeance' => '2025-03-07',
            'date_realisation' => null,
            'ecart_jours' => 143,
            'statut' => 'en_retard',
            'actif' => true
        ]);

        // SA3: Prêt relais BGFI à envisager
        SousAction::create([
            'code' => 'SA3',
            'libelle' => 'Prêt relais BGFI à envisager',
            'description' => 'Prêt relais BGFI à envisager',
            'action_id' => $action3->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2025-03-07',
            'date_realisation' => null,
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);
    }

    private function createSousActionsForA4($action4)
    {
        // SA1: Signature SPA avec conditions suspensives
        SousAction::create([
            'code' => 'SA1',
            'libelle' => 'Signature SPA avec conditions suspensives',
            'description' => 'Signature SPA avec conditions suspensives',
            'action_id' => $action4->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2024-11-30',
            'date_realisation' => '2024-10-15',
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);

        // SA2: Transfert des fonds
        SousAction::create([
            'code' => 'SA2',
            'libelle' => 'Transfert des fonds',
            'description' => 'Transfert des fonds',
            'action_id' => $action4->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2024-12-31',
            'date_realisation' => '2025-01-31',
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);

        // SA3: Finalisation des actes + signature
        SousAction::create([
            'code' => 'SA3',
            'libelle' => 'Finalisation des actes + signature',
            'description' => 'Finalisation des actes + signature',
            'action_id' => $action4->id,
            'owner_id' => User::where('name', 'DGAS')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2025-02-28',
            'date_realisation' => null,
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);
    }

    private function createActionsForPIL2($pil2)
    {
        // A1: Station Yamoussoukro MACI
        $action1 = Action::create([
            'code' => 'A1',
            'libelle' => 'Station Yamoussoukro MACI',
            'description' => 'Station Yamoussoukro MACI',
            'objectif_specifique_id' => $pil2->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 100,
            'actif' => true
        ]);

        // A2: Station [Open]
        $action2 = Action::create([
            'code' => 'A2',
            'libelle' => 'Station [Open]',
            'description' => 'Station [Open]',
            'objectif_specifique_id' => $pil2->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 8,
            'actif' => true
        ]);

        // A3: Station [Open]
        $action3 = Action::create([
            'code' => 'A3',
            'libelle' => 'Station [Open]',
            'description' => 'Station [Open]',
            'objectif_specifique_id' => $pil2->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 33,
            'actif' => true
        ]);

        // Créer les sous-actions pour PIL2
        $this->createSousActionsForPIL2A1($action1);
        $this->createSousActionsForPIL2A2($action2);
        $this->createSousActionsForPIL2A3($action3);
    }

    private function createSousActionsForPIL2A1($action1)
    {
        // SA1: Construction de la station + equipements
        SousAction::create([
            'code' => 'SA1',
            'libelle' => 'Construction de la station + equipements',
            'description' => 'Construction de la station + equipements',
            'action_id' => $action1->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2025-02-28',
            'date_realisation' => '2025-03-07',
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);

        // SA2: Recrutement et/ou transfert des équipes
        SousAction::create([
            'code' => 'SA2',
            'libelle' => 'Recrutement et/ou transfert des équipes',
            'description' => 'Recrutement et/ou transfert des équipes',
            'action_id' => $action1->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2025-02-28',
            'date_realisation' => '2025-03-07',
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);

        // SA3: Homologation et ouverture officielle
        SousAction::create([
            'code' => 'SA3',
            'libelle' => 'Homologation et ouverture officielle',
            'description' => 'Homologation et ouverture officielle',
            'action_id' => $action1->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2025-02-28',
            'date_realisation' => '2025-03-07',
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);
    }

    private function createSousActionsForPIL2A2($action2)
    {
        // SA1: Construction de la station + equipements
        SousAction::create([
            'code' => 'SA1',
            'libelle' => 'Construction de la station + equipements',
            'description' => 'Construction de la station + equipements',
            'action_id' => $action2->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 25,
            'date_echeance' => '2025-06-30',
            'date_realisation' => null,
            'ecart_jours' => 28,
            'statut' => 'en_retard',
            'actif' => true
        ]);

        // SA2: Recrutement et/ou transfert des équipes
        SousAction::create([
            'code' => 'SA2',
            'libelle' => 'Recrutement et/ou transfert des équipes',
            'description' => 'Recrutement et/ou transfert des équipes',
            'action_id' => $action2->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 0,
            'date_echeance' => '2025-06-30',
            'date_realisation' => null,
            'ecart_jours' => 28,
            'statut' => 'en_retard',
            'actif' => true
        ]);

        // SA3: Ouverture officielle
        SousAction::create([
            'code' => 'SA3',
            'libelle' => 'Ouverture officielle',
            'description' => 'Ouverture officielle',
            'action_id' => $action2->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 0,
            'date_echeance' => '2025-06-30',
            'date_realisation' => null,
            'ecart_jours' => 28,
            'statut' => 'en_retard',
            'actif' => true
        ]);
    }

    private function createSousActionsForPIL2A3($action3)
    {
        // SA1: Construction de la station + equipements
        SousAction::create([
            'code' => 'SA1',
            'libelle' => 'Construction de la station + equipements',
            'description' => 'Construction de la station + equipements',
            'action_id' => $action3->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 75,
            'date_echeance' => '2025-06-30',
            'date_realisation' => null,
            'ecart_jours' => 28,
            'statut' => 'en_retard',
            'actif' => true
        ]);

        // SA2: Recrutement et/ou transfert des équipes
        SousAction::create([
            'code' => 'SA2',
            'libelle' => 'Recrutement et/ou transfert des équipes',
            'description' => 'Recrutement et/ou transfert des équipes',
            'action_id' => $action3->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 0,
            'date_echeance' => '2025-06-30',
            'date_realisation' => null,
            'ecart_jours' => null, // #REF! dans Excel
            'statut' => 'en_retard',
            'actif' => true
        ]);

        // SA3: Ouverture officielle
        SousAction::create([
            'code' => 'SA3',
            'libelle' => 'Ouverture officielle',
            'description' => 'Ouverture officielle',
            'action_id' => $action3->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 25,
            'date_echeance' => '2025-06-30',
            'date_realisation' => null,
            'ecart_jours' => 28,
            'statut' => 'en_retard',
            'actif' => true
        ]);
    }

    private function createActionsForPIL3($pil3)
    {
        // A1: Partenariat police nationale [Open]
        $action1 = Action::create([
            'code' => 'A1',
            'libelle' => 'Partenariat police nationale [Open]',
            'description' => 'Partenariat police nationale',
            'objectif_specifique_id' => $pil3->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 63,
            'actif' => true
        ]);

        // A2: Partenarial assureurs [Open]
        $action2 = Action::create([
            'code' => 'A2',
            'libelle' => 'Partenarial assureurs [Open]',
            'description' => 'Partenarial assureurs',
            'objectif_specifique_id' => $pil3->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 0,
            'actif' => true
        ]);

        // A3: Partenariat Ministère du budget sur arriérés vignettes
        $action3 = Action::create([
            'code' => 'A3',
            'libelle' => 'Partenariat Ministère du budget sur arriérés vignettes',
            'description' => 'Partenariat Ministère du budget',
            'objectif_specifique_id' => $pil3->id,
            'owner_id' => User::where('name', 'DG SICTA')->first()->id,
            'taux_avancement' => 33,
            'actif' => true
        ]);

        // Créer les sous-actions pour PIL3
        $this->createSousActionsForPIL3A1($action1);
        $this->createSousActionsForPIL3A2($action2);
        $this->createSousActionsForPIL3A3($action3);
    }

    private function createSousActionsForPIL3A1($action1)
    {
        // SA1: Lancement contrôle systématique - Van police
        SousAction::create([
            'code' => 'SA1',
            'libelle' => 'Lancement contrôle systématique - Van police',
            'description' => 'Lancement contrôle systématique - Van police',
            'action_id' => $action1->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2025-03-31',
            'date_realisation' => null,
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);

        // SA2: Contrôle véhicules police
        SousAction::create([
            'code' => 'SA2',
            'libelle' => 'Contrôle véhicules police',
            'description' => 'Contrôle véhicules police',
            'action_id' => $action1->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 25,
            'date_echeance' => '2025-03-31',
            'date_realisation' => null,
            'ecart_jours' => 119,
            'statut' => 'en_retard',
            'actif' => true
        ]);
    }

    private function createSousActionsForPIL3A2($action2)
    {
        // SA1, SA2, SA3: Open
        for ($i = 1; $i <= 3; $i++) {
            SousAction::create([
                'code' => 'SA' . $i,
                'libelle' => 'Open',
                'description' => 'Open',
                'action_id' => $action2->id,
                'owner_id' => User::where('name', 'DG MACI')->first()->id,
                'taux_avancement' => 0,
                'date_echeance' => '2025-06-30',
                'date_realisation' => null,
                'ecart_jours' => 28,
                'statut' => 'en_retard',
                'actif' => true
            ]);
        }
    }

    private function createSousActionsForPIL3A3($action3)
    {
        // SA1: Open (100%)
        SousAction::create([
            'code' => 'SA1',
            'libelle' => 'Open',
            'description' => 'Open',
            'action_id' => $action3->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2025-06-30',
            'date_realisation' => null,
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);

        // SA2, SA3: Open (0%)
        for ($i = 2; $i <= 3; $i++) {
            SousAction::create([
                'code' => 'SA' . $i,
                'libelle' => 'Open',
                'description' => 'Open',
                'action_id' => $action3->id,
                'owner_id' => User::where('name', 'DG MACI')->first()->id,
                'taux_avancement' => 0,
                'date_echeance' => '2025-06-30',
                'date_realisation' => null,
                'ecart_jours' => 28,
                'statut' => 'en_retard',
                'actif' => true
            ]);
        }
    }

    private function createActionsForPIL4($pil4)
    {
        // A1: Mayelia MOBILITE - Projet collecte District d'Abidjan
        $action1 = Action::create([
            'code' => 'A1',
            'libelle' => 'Mayelia MOBILITE - Projet collecte District d\'Abidjan',
            'description' => 'Projet collecte District d\'Abidjan',
            'objectif_specifique_id' => $pil4->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 50,
            'actif' => true
        ]);

        // A2: Mayelia MOBILITE - Projet gares routières Mairie Cocody
        $action2 = Action::create([
            'code' => 'A2',
            'libelle' => 'Mayelia MOBILITE - Projet gares routières Mairie Cocody',
            'description' => 'Projet gares routières Mairie Cocody',
            'objectif_specifique_id' => $pil4->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 0,
            'actif' => true
        ]);

        // A3: Mayelia CI - Collecte patente transport et autres types de taxes transport
        $action3 = Action::create([
            'code' => 'A3',
            'libelle' => 'Mayelia CI - Collecte patente transport et autres types de taxes transport',
            'description' => 'Collecte patente transport et autres types de taxes transport',
            'objectif_specifique_id' => $pil4->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 0,
            'actif' => true
        ]);

        // A4: Mayelia GUINEE - Contrôle technique des pétroliers et miniers
        $action4 = Action::create([
            'code' => 'A4',
            'libelle' => 'Mayelia GUINEE - Contrôle technique des pétroliers et miniers',
            'description' => 'Contrôle technique des pétroliers et miniers',
            'objectif_specifique_id' => $pil4->id,
            'owner_id' => User::where('name', 'DG MAGUI')->first()->id,
            'taux_avancement' => 0,
            'actif' => true
        ]);

        // Créer les sous-actions pour PIL4
        $this->createSousActionsForPIL4A1($action1);
        $this->createSousActionsForPIL4A2($action2);
        $this->createSousActionsForPIL4A3($action3);
        $this->createSousActionsForPIL4A4($action4);
    }

    private function createSousActionsForPIL4A1($action1)
    {
        // SA1: Open (100%)
        SousAction::create([
            'code' => 'SA1',
            'libelle' => 'Open',
            'description' => 'Open',
            'action_id' => $action1->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 100,
            'date_echeance' => '2025-04-30',
            'date_realisation' => null,
            'ecart_jours' => 0,
            'statut' => 'termine',
            'actif' => true
        ]);

        // SA2: Open (50%)
        SousAction::create([
            'code' => 'SA2',
            'libelle' => 'Open',
            'description' => 'Open',
            'action_id' => $action1->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 50,
            'date_echeance' => '2025-04-30',
            'date_realisation' => null,
            'ecart_jours' => 89,
            'statut' => 'en_retard',
            'actif' => true
        ]);

        // SA3: Open (0%)
        SousAction::create([
            'code' => 'SA3',
            'libelle' => 'Open',
            'description' => 'Open',
            'action_id' => $action1->id,
            'owner_id' => User::where('name', 'DG MACI')->first()->id,
            'taux_avancement' => 0,
            'date_echeance' => '2025-04-30',
            'date_realisation' => null,
            'ecart_jours' => 89,
            'statut' => 'en_retard',
            'actif' => true
        ]);
    }

    private function createSousActionsForPIL4A2($action2)
    {
        // SA1, SA2, SA3: Open (0%)
        for ($i = 1; $i <= 3; $i++) {
            SousAction::create([
                'code' => 'SA' . $i,
                'libelle' => 'Open',
                'description' => 'Open',
                'action_id' => $action2->id,
                'owner_id' => User::where('name', 'DG MACI')->first()->id,
                'taux_avancement' => 0,
                'date_echeance' => '2025-06-30',
                'date_realisation' => null,
                'ecart_jours' => 28,
                'statut' => 'en_retard',
                'actif' => true
            ]);
        }
    }

    private function createSousActionsForPIL4A3($action3)
    {
        // SA1, SA2, SA3: Open (0%)
        for ($i = 1; $i <= 3; $i++) {
            SousAction::create([
                'code' => 'SA' . $i,
                'libelle' => 'Open',
                'description' => 'Open',
                'action_id' => $action3->id,
                'owner_id' => User::where('name', 'DG MACI')->first()->id,
                'taux_avancement' => 0,
                'date_echeance' => '2025-06-30',
                'date_realisation' => null,
                'ecart_jours' => 28,
                'statut' => 'en_retard',
                'actif' => true
            ]);
        }
    }

    private function createSousActionsForPIL4A4($action4)
    {
        // SA1, SA2, SA3: Open (0%)
        for ($i = 1; $i <= 3; $i++) {
            SousAction::create([
                'code' => 'SA' . $i,
                'libelle' => 'Open',
                'description' => 'Open',
                'action_id' => $action4->id,
                'owner_id' => User::where('name', 'DG MAGUI')->first()->id,
                'taux_avancement' => 0,
                'date_echeance' => '2025-03-31',
                'date_realisation' => null,
                'ecart_jours' => 119,
                'statut' => 'en_retard',
                'actif' => true
            ]);
        }
    }
} 