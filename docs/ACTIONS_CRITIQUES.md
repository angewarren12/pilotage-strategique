# 🚨 Actions Critiques - Système de Validation

## 📋 Vue d'ensemble

Ce document détaille toutes les actions critiques qui nécessitent une validation hiérarchique dans le système de pilotage stratégique.

## 🎯 Catégories d'actions critiques

### 1. 🔄 **Actions de Gestion des Responsabilités**

#### `change_owner` - Changement de propriétaire

-   **Description** : Modification du propriétaire responsable d'un élément
-   **Impact** : Affecte la responsabilité, les permissions et les notifications
-   **Validation requise** : Propriétaire parent + Admin général
-   **Exemple** : Changement du propriétaire d'une action de "John Doe" à "Jane Smith"

#### `change_responsibility` - Changement de responsabilité

-   **Description** : Modification des responsabilités assignées
-   **Impact** : Affecte la répartition des tâches
-   **Validation requise** : Propriétaire parent
-   **Exemple** : Réassignation des responsabilités d'une action

### 2. 💰 **Actions Budgétaires**

#### `change_budget` - Modification budgétaire importante

-   **Description** : Changement significatif du budget alloué
-   **Impact** : Affecte les ressources financières et la planification
-   **Validation requise** : Propriétaire parent + Admin général
-   **Exemple** : Augmentation du budget d'une action de 10 000€ à 15 000€

#### `change_budget_allocation` - Réallocation budgétaire

-   **Description** : Redistribution des budgets entre éléments
-   **Impact** : Affecte l'équilibre financier
-   **Validation requise** : Propriétaire parent + Admin général

#### `increase_budget` - Augmentation de budget

-   **Description** : Augmentation du budget alloué
-   **Impact** : Impact financier positif
-   **Validation requise** : Propriétaire parent + Admin général

#### `decrease_budget` - Diminution de budget

-   **Description** : Réduction du budget alloué
-   **Impact** : Impact financier négatif, peut affecter la réalisation
-   **Validation requise** : Propriétaire parent + Admin général

### 3. ⏰ **Actions Temporelles**

#### `change_deadline` - Modification d'échéance

-   **Description** : Changement de la date d'échéance
-   **Impact** : Affecte la planification et les délais
-   **Validation requise** : Propriétaire parent + Admin général
-   **Exemple** : Extension de l'échéance d'une action de 3 mois

#### `extend_deadline` - Extension d'échéance

-   **Description** : Prolongation de la date d'échéance
-   **Impact** : Impact positif sur la planification
-   **Validation requise** : Propriétaire parent + Admin général

#### `advance_deadline` - Avancement d'échéance

-   **Description** : Anticipation de la date d'échéance
-   **Impact** : Impact négatif sur la planification
-   **Validation requise** : Propriétaire parent + Admin général

#### `change_start_date` - Modification de date de début

-   **Description** : Changement de la date de début
-   **Impact** : Affecte la planification initiale
-   **Validation requise** : Propriétaire parent

### 4. 📊 **Actions de Statut**

#### `change_status` - Changement de statut critique

-   **Description** : Modification du statut d'un élément
-   **Impact** : Affecte l'état d'avancement et les métriques
-   **Validation requise** : Propriétaire parent + Admin général
-   **Exemple** : Passage d'une action de "En cours" à "Terminé"

#### `mark_completed` - Marquer comme terminé

-   **Description** : Marquage d'un élément comme terminé
-   **Impact** : Affecte les métriques de progression
-   **Validation requise** : Propriétaire parent + Admin général

#### `mark_cancelled` - Marquer comme annulé

-   **Description** : Annulation d'un élément
-   **Impact** : Impact négatif sur les objectifs
-   **Validation requise** : Propriétaire parent + Admin général

#### `mark_on_hold` - Mettre en attente

-   **Description** : Mise en pause d'un élément
-   **Impact** : Affecte la progression
-   **Validation requise** : Propriétaire parent

### 5. 🗑️ **Actions de Suppression**

#### `delete_element` - Suppression d'élément

-   **Description** : Suppression définitive d'un élément
-   **Impact** : Perte de données et impact sur la hiérarchie
-   **Validation requise** : Propriétaire parent + Admin général
-   **Exemple** : Suppression d'une action et de ses sous-actions

#### `archive_element` - Archivage d'élément

-   **Description** : Archivage d'un élément (conservation des données)
-   **Impact** : Affecte la visibilité mais conserve les données
-   **Validation requise** : Propriétaire parent + Admin général

#### `bulk_delete` - Suppression en lot

-   **Description** : Suppression multiple d'éléments
-   **Impact** : Impact majeur sur la structure
-   **Validation requise** : Admin général uniquement

### 6. 🎯 **Actions de Priorité et Organisation**

#### `change_priority` - Changement de priorité

-   **Description** : Modification de la priorité d'un élément
-   **Impact** : Affecte l'ordre d'exécution
-   **Validation requise** : Propriétaire parent
-   **Exemple** : Passage d'une action de priorité "Basse" à "Haute"

#### `change_order` - Changement d'ordre

-   **Description** : Modification de l'ordre d'exécution
-   **Impact** : Affecte la séquence de travail
-   **Validation requise** : Propriétaire parent

#### `reorganize_hierarchy` - Réorganisation hiérarchique

-   **Description** : Restructuration de la hiérarchie
-   **Impact** : Impact majeur sur l'organisation
-   **Validation requise** : Admin général uniquement

### 7. 📝 **Actions de Contenu**

#### `change_description` - Modification de description majeure

-   **Description** : Changement significatif de la description
-   **Impact** : Affecte la compréhension du projet
-   **Validation requise** : Propriétaire parent
-   **Exemple** : Modification complète de la description d'un objectif stratégique

#### `change_objectives` - Modification d'objectifs

-   **Description** : Changement des objectifs définis
-   **Impact** : Affecte la direction du projet
-   **Validation requise** : Propriétaire parent + Admin général

#### `change_scope` - Modification de périmètre

-   **Description** : Changement du périmètre d'un élément
-   **Impact** : Affecte la portée du projet
-   **Validation requise** : Propriétaire parent + Admin général

### 8. 🏗️ **Actions de Structure**

#### `change_code` - Modification du code hiérarchique

-   **Description** : Changement du code de référence
-   **Impact** : Affecte la structure et les références
-   **Validation requise** : Admin général uniquement
-   **Exemple** : Changement du code "P1.OS1.OSP1" vers "P1.OS1.OSP2"

#### `change_structure` - Modification de structure

-   **Description** : Changement de la structure organisationnelle
-   **Impact** : Impact majeur sur l'organisation
-   **Validation requise** : Admin général uniquement

#### `merge_elements` - Fusion d'éléments

-   **Description** : Fusion de plusieurs éléments
-   **Impact** : Affecte la structure et les données
-   **Validation requise** : Admin général uniquement

#### `split_element` - Division d'élément

-   **Description** : Division d'un élément en plusieurs
-   **Impact** : Affecte la structure et les données
-   **Validation requise** : Admin général uniquement

### 9. 🎨 **Actions d'Identité**

#### `change_color` - Modification de la couleur du pilier

-   **Description** : Changement de la couleur d'identité
-   **Impact** : Affecte l'identité visuelle
-   **Validation requise** : Propriétaire du pilier + Admin général
-   **Exemple** : Changement de la couleur d'un pilier de bleu à vert

#### `change_name` - Modification du nom

-   **Description** : Changement du nom d'un élément
-   **Impact** : Affecte l'identification
-   **Validation requise** : Propriétaire parent

#### `change_identity` - Modification d'identité

-   **Description** : Changement complet de l'identité
-   **Impact** : Impact majeur sur l'identification
-   **Validation requise** : Admin général uniquement

### 10. 📦 **Actions en Lot**

#### `bulk_update` - Mise à jour en lot

-   **Description** : Modifications multiples simultanées
-   **Impact** : Impact majeur sur plusieurs éléments
-   **Validation requise** : Admin général uniquement

#### `bulk_status_change` - Changement de statut en lot

-   **Description** : Modification du statut de plusieurs éléments
-   **Impact** : Affecte plusieurs métriques
-   **Validation requise** : Admin général uniquement

#### `bulk_owner_change` - Changement de propriétaire en lot

-   **Description** : Modification du propriétaire de plusieurs éléments
-   **Impact** : Affecte plusieurs responsabilités
-   **Validation requise** : Admin général uniquement

### 11. 🔒 **Actions de Sécurité**

#### `change_permissions` - Modification de permissions

-   **Description** : Changement des permissions d'accès
-   **Impact** : Affecte la sécurité
-   **Validation requise** : Admin général uniquement

#### `change_access_rights` - Modification de droits d'accès

-   **Description** : Changement des droits d'accès
-   **Impact** : Affecte la sécurité
-   **Validation requise** : Admin général uniquement

#### `change_visibility` - Modification de visibilité

-   **Description** : Changement de la visibilité d'un élément
-   **Impact** : Affecte l'accès aux informations
-   **Validation requise** : Propriétaire parent + Admin général

## 🔄 Workflow de validation

### Étape 1 : Détection

-   Le système détecte automatiquement une action critique
-   Vérification si la validation est nécessaire

### Étape 2 : Création de la demande

-   Création automatique d'une demande de validation
-   Notification des validateurs potentiels

### Étape 3 : Validation

-   Les validateurs examinent la demande
-   Décision d'approbation ou de rejet

### Étape 4 : Application

-   Si approuvé : application automatique des changements
-   Si rejeté : conservation de l'état initial

## 📊 Métriques de validation

-   **Taux d'approbation** : Pourcentage de validations approuvées
-   **Temps de validation** : Délai moyen de traitement
-   **Validations expirées** : Nombre de demandes expirées
-   **Validations en attente** : Nombre de demandes en cours

## 🎯 Bonnes pratiques

1. **Justification** : Toujours fournir une raison claire pour les actions critiques
2. **Communication** : Informer les parties prenantes des changements
3. **Documentation** : Conserver un historique complet des validations
4. **Formation** : Former les utilisateurs aux actions critiques
5. **Monitoring** : Surveiller régulièrement les métriques de validation
