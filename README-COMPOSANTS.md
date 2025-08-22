# 📋 Documentation des Composants Livewire - Vue Détaillée

## 🎯 **Vue Détaillée d'un Pilier**

### **Composant :** `PilierHierarchiqueV2`

### **Fichier :** `resources/views/livewire/pilier-hierarchique-v2/index.blade.php`

### **Fonctionnalités :**

-   ✅ **Affichage du pilier** avec informations complètes
-   ✅ **Liste des objectifs stratégiques** avec progression
-   ✅ **Création d'objectifs stratégiques** avec modal
-   ✅ **Édition d'objectifs stratégiques** avec validation
-   ✅ **Suppression d'objectifs stratégiques** avec confirmation
-   ✅ **Navigation vers les détails** des objectifs stratégiques
-   ✅ **Système de notifications** pour les propriétaires
-   ✅ **Gestion des permissions** (création, édition, suppression)

---

## 🎯 **Vue Détaillée d'un Objectif Stratégique**

### **Composant :** `objectif-strategique-list.blade.php`

### **Fichier :** `resources/views/livewire/pilier-hierarchique-v2/components/objectif-strategique-list.blade.php`

### **Fonctionnalités :**

-   ✅ **Informations détaillées** de l'objectif stratégique
-   ✅ **Indicateur de progression** circulaire SVG
-   ✅ **Statistiques complètes** (OSP, Actions, Sous-Actions, Terminés)
-   ✅ **Liste des objectifs spécifiques** avec tableau interactif
-   ✅ **Actions CRUD** (Voir, Modifier, Supprimer)
-   ✅ **Navigation vers l'objectif spécifique**
-   ✅ **Actions rapides** et retour au pilier
-   ✅ **Design responsive** avec animations CSS

### **Éléments visuels :**

-   🎨 **Icône circulaire** avec couleur hiérarchique
-   📊 **Graphique de progression** SVG animé
-   🃏 **Cartes statistiques** avec effets hover
-   📱 **Interface responsive** pour mobile et desktop
-   ✨ **Animations CSS** et transitions fluides

---

## 🎯 **Vue Détaillée d'un Objectif Spécifique**

### **Composant :** `objectif-specifique-detail.blade.php`

### **Fichier :** `resources/views/livewire/pilier-hierarchique-v2/components/objectif-specifique-detail.blade.php`

### **Fonctionnalités :**

-   ✅ **Informations détaillées** de l'objectif spécifique
-   ✅ **Contexte hiérarchique** (Pilier > OS > OSP)
-   ✅ **Indicateur de progression** avec date d'échéance
-   ✅ **Statistiques des actions** (Total, Terminées, À démarrer)
-   ✅ **Liste des actions** avec tableau interactif
-   ✅ **Actions CRUD** pour les actions
-   ✅ **Navigation vers les détails** des actions
-   ✅ **Actions rapides** et retour à l'OS

### **Éléments visuels :**

-   🎨 **Icône hiérarchique** avec couleur de niveau
-   📅 **Badge de date d'échéance** avec couleur conditionnelle
-   📊 **Graphique de progression** SVG
-   🃏 **Cartes statistiques** avec effets hover
-   📱 **Interface responsive** et accessible

---

## 🔧 **Méthodes Livewire Requises**

### **Navigation :**

```php
public function naviguerVersObjectifStrategique($objectifId)
public function naviguerVersObjectifSpecifique($objectifSpecifiqueId)
public function naviguerVersAction($actionId)
public function retourVersPilier()
public function retourVersObjectifStrategique()
```

### **Permissions :**

```php
public function canCreateObjectifSpecifique()
public function canEditObjectifSpecifique($objectifSpecifique)
public function canDeleteObjectifSpecifique($objectifSpecifique)
public function canCreateAction()
public function canEditAction($action)
public function canDeleteAction($action)
```

### **CRUD :**

```php
public function openCreateOSPModal()
public function setObjectifSpecifiqueToEdit($id)
public function deleteObjectifSpecifique($id)
public function openCreateActionModal()
public function setActionToEdit($id)
public function deleteAction($id)
```

---

## 🎨 **Système de Couleurs Hiérarchiques**

### **Méthodes du modèle Pilier :**

```php
public function getHierarchicalColor($level)
public function getTextColor($backgroundColor)
```

### **Niveaux de couleur :**

-   **Niveau 1** : Pilier (couleur principale)
-   **Niveau 2** : Objectif Stratégique
-   **Niveau 3** : Objectif Spécifique
-   **Niveau 4** : Action
-   **Niveau 5** : Sous-Action

---

## 📱 **Responsive Design**

### **Breakpoints :**

-   **Desktop** : ≥ 768px (col-md-\*)
-   **Mobile** : < 768px (col-\*)

### **Adaptations mobiles :**

-   Réorganisation des colonnes
-   Boutons adaptés aux écrans tactiles
-   Espacement optimisé pour mobile
-   Navigation simplifiée

---

## 🚀 **Utilisation**

### **1. Inclure le composant :**

```blade
@livewire('pilier-hierarchique-v2', ['pilier' => $pilier])
```

### **2. Navigation automatique :**

-   Clic sur l'œil du pilier → Vue détail pilier
-   Clic sur l'œil de l'OS → Vue détail OS
-   Clic sur l'œil de l'OSP → Vue détail OSP
-   Boutons de retour pour navigation inverse

### **3. Permissions automatiques :**

-   Vérification des droits utilisateur
-   Affichage conditionnel des boutons
-   Validation des actions

---

## 🔍 **Débogage et Tests**

### **Logs de notification :**

```php
Log::info('📧 Envoi notification au propriétaire', [...]);
Log::info('✅ Notification envoyée avec succès', [...]);
Log::error('❌ Erreur envoi notification', [...]);
```

### **Composant de test :**

```blade
@include('livewire.pilier-hierarchique-v2.components.test-component')
```

---

## 📋 **Statut d'Implémentation**

### **✅ Terminé :**

-   Vue détail du pilier
-   Vue détail de l'objectif stratégique
-   Vue détail de l'objectif spécifique
-   Système de notifications
-   Gestion des permissions
-   Design responsive
-   Animations CSS

### **🔄 En cours :**

-   Tests d'intégration
-   Optimisations de performance

### **📝 À faire :**

-   Tests unitaires
-   Documentation utilisateur
-   Guide de déploiement

---

## 🎯 **Prochaines Étapes**

1. **Tester la navigation** entre les vues
2. **Vérifier les permissions** utilisateur
3. **Tester les notifications** de création/modification
4. **Valider le responsive** sur différents appareils
5. **Implémenter les tests** automatisés

---

## 📞 **Support**

Pour toute question ou problème :

-   Vérifier les logs Laravel
-   Tester les permissions utilisateur
-   Valider la configuration Livewire
-   Consulter la documentation Laravel/Livewire
