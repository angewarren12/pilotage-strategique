# 🚨 Test du Système de Notifications de Relance

## 📋 **Vue d'Ensemble du Système**

Le système de notifications de relance fonctionne automatiquement et envoie des alertes aux propriétaires des sous-actions selon les échéances.

## ⏰ **Échéances de Relance**

| Jours avant échéance | Type de notification                  | Priorité | Couleur | Action requise         |
| -------------------- | ------------------------------------- | -------- | ------- | ---------------------- |
| **30 jours**         | 🟡 Relance - 1 mois avant échéance    | Normal   | Jaune   | Planification          |
| **7 jours**          | 🟠 Relance - 1 semaine avant échéance | Haute    | Orange  | Accélération           |
| **3 jours**          | 🔴 Relance - 3 jours avant échéance   | Urgente  | Rouge   | Mobilisation immédiate |
| **En retard**        | 🚨 SOUS-ACTION EN RETARD !            | Critique | Rouge   | Action immédiate       |

## 🧪 **Comment Tester le Système**

### **1. Test Manuel de la Commande**

```bash
# Exécuter la commande manuellement
php artisan sous-actions:check-deadlines

# Vérifier les logs
tail -f storage/logs/sous-actions-deadlines.log
```

### **2. Test avec Données Réelles**

#### **Scénario 1 : Relance 1 mois avant**

-   **Date échéance** : 22/09/2025 (dans 30 jours)
-   **Taux** : 45%
-   **Résultat attendu** : Notification "🟡 Relance - 1 mois avant échéance"

#### **Scénario 2 : Relance 1 semaine avant**

-   **Date échéance** : 29/08/2025 (dans 7 jours)
-   **Taux** : 60%
-   **Résultat attendu** : Notification "🟠 Relance - 1 semaine avant échéance"

#### **Scénario 3 : Relance 3 jours avant**

-   **Date échéance** : 25/08/2025 (dans 3 jours)
-   **Taux** : 75%
-   **Résultat attendu** : Notification "🔴 Relance - 3 jours avant échéance"

#### **Scénario 4 : Sous-action en retard**

-   **Date échéance** : 20/08/2025 (passée)
-   **Taux** : 80%
-   **Résultat attendu** : Notification "🚨 SOUS-ACTION EN RETARD !"

### **3. Vérification des Notifications**

#### **Dans la Base de Données**

```sql
-- Voir toutes les notifications de relance
SELECT * FROM notifications
WHERE type LIKE 'reminder_%'
ORDER BY created_at DESC;

-- Voir les notifications de retard
SELECT * FROM notifications
WHERE type = 'delay_notification'
ORDER BY created_at DESC;
```

#### **Dans les Logs Laravel**

```bash
# Voir les logs de la commande
tail -f storage/logs/laravel.log | grep "Notification de relance"

# Voir les logs spécifiques
tail -f storage/logs/sous-actions-deadlines.log
```

## 🔧 **Configuration Automatique**

### **Planificateur de Tâches**

-   **Fréquence** : Quotidienne
-   **Heure** : 9h00 du matin
-   **Logs** : `storage/logs/sous-actions-deadlines.log`

### **Activation du Planificateur**

```bash
# Sur le serveur de production
crontab -e

# Ajouter cette ligne
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## 📱 **Types de Notifications Envoyées**

### **1. Relance 1 mois avant**

```
🟡 Relance - 1 mois avant échéance

Rappel : Votre sous-action 'Signature SPA' arrive à échéance dans 1 mois
(le 22/09/2025). Progression actuelle : 45%.
Pensez à accélérer le travail !
```

### **2. Relance 1 semaine avant**

```
🟠 Relance - 1 semaine avant échéance

URGENT : Votre sous-action 'Signature SPA' arrive à échéance dans 1 semaine
(le 29/08/2025). Progression actuelle : 60%.
Action immédiate requise !
```

### **3. Relance 3 jours avant**

```
🔴 Relance - 3 jours avant échéance

CRITIQUE : Votre sous-action 'Signature SPA' arrive à échéance dans 3 jours
(le 25/08/2025). Progression actuelle : 75%.
Mobilisation immédiate !
```

### **4. Notification de retard**

```
🚨 SOUS-ACTION EN RETARD !

Votre sous-action 'Signature SPA' est en retard depuis le 20/08/2025.
Progression actuelle : 80%.
```

## 🚀 **Avantages du Système**

1. **⏰ Automatique** : Pas d'intervention manuelle requise
2. **🎯 Préventif** : Alertes avant les retards
3. **📊 Progressif** : Intensité croissante des alertes
4. **📝 Traçable** : Logs complets de toutes les actions
5. **🔄 Évite le spam** : Maximum 1 notification par jour par seuil
6. **⚡ Priorisé** : Notifications urgentes bien visibles

## 🔍 **Dépannage**

### **Problème : Pas de notifications envoyées**

1. Vérifier que la commande s'exécute : `php artisan sous-actions:check-deadlines`
2. Vérifier les logs : `tail -f storage/logs/sous-actions-deadlines.log`
3. Vérifier que les sous-actions ont des `date_echeance` et `taux_avancement < 100`

### **Problème : Trop de notifications**

1. Vérifier la logique de `shouldSendNotification()`
2. Vérifier que les notifications récentes sont bien détectées
3. Ajuster le délai dans `now()->subDays(1)` si nécessaire

### **Problème : Planificateur ne fonctionne pas**

1. Vérifier que cron est activé sur le serveur
2. Vérifier la commande : `php artisan schedule:list`
3. Tester manuellement : `php artisan schedule:run`
