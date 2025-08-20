# Gestion Automatique du Statut des Activités

## Vue d'ensemble

Ce système gère automatiquement le statut des activités selon leur date de début, en respectant les règles métier suivantes :

-   **Statut "À faire"** : Activité dont la date de début est dans le futur (> aujourd'hui)
-   **Statut "En cours"** : Activité dont la date de début est aujourd'hui ou dans le passé (≤ aujourd'hui)
-   **Statut "Terminé"** : Activité avec une progression de 100%
-   **Statut "Bloqué"** : Activité marquée manuellement comme bloquée

## Fonctionnalités

### 1. Validation des Progression

-   **Activités non commencées** : Impossible de modifier la progression si `date_debut > aujourd'hui`
-   **Activités en cours** : Progression modifiable librement
-   **Mise à jour en temps réel** : Changements reflétés immédiatement dans l'interface

### 2. Mise à Jour Automatique du Statut

-   **Au chargement de la page** : Vérification et mise à jour automatique
-   **Via commande Artisan** : Mise à jour en lot de toutes les activités
-   **Planification automatique** : Exécution quotidienne à 00:01

### 3. Interface Utilisateur

-   **Bouton "Modifier"** : Actif uniquement pour les activités commencées
-   **Indicateur visuel** : Bouton désactivé avec icône d'horloge pour les activités futures
-   **Messages informatifs** : Affichage de la date de début pour les activités non commencées

## Utilisation

### Interface Web

1. **Accéder à la gestion des activités** : Cliquer sur "Gérer les activités" depuis la vue hiérarchique
2. **Voir le statut automatique** : Le statut est affiché avec des couleurs distinctives
3. **Modifier la progression** : Utiliser le bouton "Modifier" (actif uniquement pour les activités commencées)

### Commandes Artisan

#### Simulation (sans modification)

```bash
php artisan activities:update-status --dry-run
```

#### Application des changements

```bash
php artisan activities:update-status
```

#### Planification automatique

```bash
# Ajouter au cron du serveur
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Structure Technique

### Modèle Activity

-   **Accesseurs** : `a_commence`, `peut_etre_modifiee`
-   **Méthodes** : `determinerStatutParDate()`, `updateStatutAutomatique()`
-   **Validation** : Vérification de la possibilité de modification

### Contrôleur ActivityController

-   **Validation** : Vérification de la possibilité de modification avant mise à jour
-   **Gestion d'erreurs** : Messages d'erreur appropriés pour les activités non commencées
-   **Logs** : Traçabilité complète des opérations

### Vue manage.blade.php

-   **Affichage conditionnel** : Boutons actifs/inactifs selon l'état de l'activité
-   **Mise à jour en temps réel** : JavaScript pour les interactions dynamiques
-   **Validation côté client** : Vérification avant envoi des requêtes

## Règles Métier

### 1. Statut Automatique par Date

```php
if (date_debut > aujourd'hui) {
    statut = 'en_attente';
} else {
    statut = 'en_cours';
}
```

### 2. Statut par Progression

```php
if (progression >= 100) {
    statut = 'termine';
} elseif (progression >= 1) {
    statut = 'en_cours';
} else {
    statut = 'en_attente';
}
```

### 3. Validation de Modification

```php
if (!activite.a_commence) {
    throw new Exception('Impossible de modifier la progression d\'une activité non commencée');
}
```

## Logs et Monitoring

### Fichiers de Log

-   **Laravel** : `storage/logs/laravel.log`
-   **Commandes** : `storage/logs/activities-status-update.log`

### Types de Logs

-   **INFO** : Opérations réussies
-   **WARNING** : Erreurs non critiques
-   **ERROR** : Erreurs critiques

## Maintenance

### Vérification du Système

1. **Tester la commande** : `php artisan activities:update-status --dry-run`
2. **Vérifier les logs** : Contrôler les fichiers de log
3. **Tester l'interface** : Vérifier le comportement des boutons

### Dépannage

-   **Activités non mises à jour** : Vérifier les permissions de base de données
-   **Erreurs de recalcul** : Contrôler les relations entre activités et sous-actions
-   **Problèmes d'interface** : Vérifier la console JavaScript du navigateur

## Évolutions Futures

-   **Notifications** : Alertes pour les changements de statut
-   **Historique** : Traçabilité des modifications de statut
-   **Rapports** : Statistiques sur l'évolution des statuts
-   **API** : Endpoints pour la gestion programmatique
