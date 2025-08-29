# 📋 **Documentation des Logs - Notifications d'Activités**

## 🎯 **Objectif**

Ce document explique comment tester et vérifier que les notifications sont bien envoyées lorsque la progression d'une activité passe à 100%.

## 🔍 **Logs Ajoutés**

### **1. Méthode `updateActivityProgress()`**

Cette méthode est appelée quand l'utilisateur modifie la progression d'une activité via le slider.

#### **Logs de début**

```
🔄 Début de mise à jour de la progression d'activité
- activity_id: ID de l'activité
- nouvelle_progression: Nouvelle valeur (0-100)
- user_id: ID de l'utilisateur qui fait la modification
- timestamp: Horodatage ISO
```

#### **Logs de validation**

```
📊 Activité trouvée avant mise à jour
- activity_id: ID de l'activité
- ancienne_progression: Progression avant modification
- nouvelle_progression: Progression après modification
- titre: Titre de l'activité
- sous_action_id: ID de la sous-action parente
- owner_id: ID du propriétaire
- ancien_statut: Statut avant modification
```

#### **Logs de détection 100%**

```
🎯 ACTIVITÉ PASSE À 100% - Préparation de la notification
- activity_id: ID de l'activité
- ancienne_progression: Progression avant (doit être < 100)
- nouvelle_progression: 100
- titre: Titre de l'activité
- owner_id: ID du propriétaire
```

#### **Logs de notification**

```
🔔 ENVOI DE NOTIFICATION - Activité terminée à 100%
- activity_id: ID de l'activité
- owner_id: ID du propriétaire
- titre: Titre de l'activité
- progression: 100
- sous_action_id: ID de la sous-action parente
```

#### **Logs de succès notification**

```
✅ NOTIFICATION ENVOYÉE avec succès pour l'activité terminée
- activity_id: ID de l'activité
- owner_id: ID du propriétaire
- notification_type: 'activity_completed'
- timestamp: Horodatage ISO
```

### **2. Méthode `recalculateProjectProgress()`**

Cette méthode est appelée automatiquement pour recalculer la progression d'un projet basée sur ses activités.

#### **Logs de début**

```
🔄 Début du recalcul de la progression du projet
- sous_action_id: ID de la sous-action projet
- timestamp: Horodatage ISO
```

#### **Logs d'informations projet**

```
📊 Informations de la sous-action projet
- sous_action_id: ID de la sous-action
- type: Type (doit être 'projet')
- libelle: Libellé du projet
- ancienne_progression: Progression actuelle
- owner_id: ID du propriétaire
```

#### **Logs des activités**

```
📋 Activités trouvées pour le projet
- sous_action_id: ID de la sous-action
- nombre_activites: Nombre total d'activités
- activites_details: Détails de chaque activité (ID, titre, progression, statut)
```

#### **Logs de calcul**

```
🧮 Calcul de la nouvelle progression du projet
- sous_action_id: ID de la sous-action
- total_progression_activites: Somme des progressions
- nombre_activites: Nombre d'activités
- progression_moyenne: Moyenne calculée
- progression_arrondie: Progression finale arrondie
- ancienne_progression: Progression avant mise à jour
```

#### **Logs de détection 100% projet**

```
🎯 PROJET PASSE À 100% - Préparation de la notification
- sous_action_id: ID de la sous-action
- ancienne_progression: Progression avant (doit être < 100)
- nouvelle_progression: 100
- libelle: Libellé du projet
- owner_id: ID du propriétaire
```

#### **Logs de gestion de la date de réalisation**

```
📅 Date de réalisation ajoutée au projet terminé
- sous_action_id: ID de la sous-action
- date_realisation: Date et heure de réalisation (ISO)
- libelle: Libellé du projet
```

#### **Logs de suppression de la date de réalisation**

```
📅 Date de réalisation supprimée du projet (progression < 100%)
- sous_action_id: ID de la sous-action
- ancienne_date_realisation: Date de réalisation précédente
- nouvelle_progression: Nouvelle progression
```

## 🧪 **Comment Tester**

### **1. Préparer l'environnement**

```bash
# Vérifier que le serveur Laravel tourne
php artisan serve

# Vérifier les logs (dans storage/logs/laravel.log)
tail -f storage/logs/laravel.log
```

### **2. Scénario de test**

1. **Ouvrir le modal des activités** d'une sous-action de type "Projet"
2. **Créer une activité** avec une progression initiale < 100%
3. **Modifier la progression** de l'activité à 100% via le slider
4. **Observer les logs** dans la console Laravel

### **3. Logs attendus lors du passage à 100%**

```
🔄 Début de mise à jour de la progression d'activité
📊 Activité trouvée avant mise à jour
🎯 ACTIVITÉ PASSE À 100% - Préparation de la notification
🔄 Mise à jour du statut de l'activité
✅ Progression de l'activité mise à jour avec succès
🔔 ENVOI DE NOTIFICATION - Activité terminée à 100%
✅ NOTIFICATION ENVOYÉE avec succès pour l'activité terminée
🔄 Début du recalcul de la progression du projet
📊 Informations de la sous-action projet
📋 Activités trouvées pour le projet
🧮 Calcul de la nouvelle progression du projet
🎯 PROJET PASSE À 100% - Préparation de la notification (si applicable)
📅 Date de réalisation ajoutée au projet terminé (si applicable)
✅ Progression de la sous-action projet mise à jour
🔄 Début de la mise à jour des taux parents
✅ Mise à jour des taux parents terminée
✅ Progression du projet recalculée avec succès
🔄 Données rafraîchies après mise à jour
```

## 🚨 **Logs d'erreur possibles**

### **Erreur de notification**

```
❌ Erreur lors de l'envoi de la notification pour l'activité terminée
- activity_id: ID de l'activité
- owner_id: ID du propriétaire
- error: Message d'erreur
- trace: Stack trace complet
```

### **Erreur de mise à jour**

```
❌ Échec de la mise à jour de la progression de l'activité
- activity_id: ID de l'activité
- nouvelle_progression: Valeur tentée
```

### **Erreur de recalcul**

```
❌ Erreur lors du recalcul de la progression du projet
- sous_action_id: ID de la sous-action
- error: Message d'erreur
- trace: Stack trace complet
```

## 📊 **Vérification des notifications**

### **1. Dans la base de données**

Vérifier la table `notifications` :

```sql
SELECT * FROM notifications
WHERE type = 'activity_completed'
ORDER BY created_at DESC
LIMIT 5;
```

### **2. Dans l'interface utilisateur**

-   Vérifier que le toast de succès s'affiche
-   Vérifier que la progression se met à jour en temps réel
-   Vérifier que le statut de l'activité passe à "Terminé"

### **3. Dans les logs**

-   Tous les logs doivent s'afficher sans erreur
-   Les timestamps doivent être cohérents
-   Les IDs doivent correspondre entre les différents logs

## 🔧 **Dépannage**

### **Si les logs n'apparaissent pas**

1. Vérifier que `Log::info()` est bien appelé
2. Vérifier les permissions sur `storage/logs/`
3. Vérifier la configuration de logging dans `config/logging.php`

### **Si les notifications ne sont pas envoyées**

1. Vérifier que la méthode `sendNotification()` existe
2. Vérifier que l'`owner_id` est bien défini
3. Vérifier les logs d'erreur de notification

### **Si la progression ne se met pas à jour**

1. Vérifier que la méthode `recalculateProjectProgress()` est appelée
2. Vérifier que les activités sont bien liées à la sous-action
3. Vérifier les logs de mise à jour des taux parents
