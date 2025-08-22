# 🧪 **Test de la Vue Détail Objectif Spécifique**

## 📋 **Vérifications à Effectuer**

### **1. Affichage des Informations**

-   ✅ **Champ `date_echeance` supprimé** : Plus d'affichage de date d'échéance
-   ✅ **Taux d'avancement en lecture seule** : Affiché mais non modifiable
-   ✅ **Informations de base** : Code, libellé, description, propriétaire
-   ✅ **Navigation** : Bouton retour vers Objectif Stratégique

### **2. Cartes des Parents (2 cartes)**

-   ✅ **Carte 1** : Détails du Pilier parent
-   ✅ **Carte 2** : Détails de l'Objectif Stratégique parent
-   ✅ **Responsivité** : 2 cartes sur même ligne (grand écran) ou en colonne (petit écran)

### **3. Liste des Actions**

-   ✅ **Affichage des actions** : Tableau avec toutes les colonnes
-   ✅ **Boutons d'action** : Voir, Modifier, Supprimer selon les permissions
-   ✅ **Informations des actions** : Code, libellé, progression, responsable, sous-actions

### **4. Opérations CRUD**

#### **Création d'Objectif Spécifique :**

-   ✅ **Modal de création** : S'ouvre correctement
-   ✅ **Champs requis** : Code, libellé, propriétaire
-   ✅ **Champs absents** : Pas de `date_echeance`, pas de `taux_avancement`
-   ✅ **Validation** : Messages d'erreur appropriés
-   ✅ **Notification** : Toast de succès/échec
-   ✅ **Permission** : Vérification `canCreateObjectifSpecifique`

#### **Modification d'Objectif Spécifique :**

-   ✅ **Modal d'édition** : S'ouvre avec les bonnes données
-   ✅ **Champs pré-remplis** : Code, libellé, description, propriétaire
-   ✅ **Champs absents** : Pas de `date_echeance`, pas de `taux_avancement`
-   ✅ **Validation** : Messages d'erreur appropriés
-   ✅ **Notification** : Toast de succès/échec
-   ✅ **Permission** : Vérification `canEditObjectifSpecifique`

#### **Suppression d'Objectif Spécifique :**

-   ✅ **Confirmation** : Demande de confirmation avant suppression
-   ✅ **Notification** : Toast de succès/échec
-   ✅ **Permission** : Vérification `canDeleteObjectifSpecifique`

### **5. Navigation**

-   ✅ **Vers Action** : Bouton pour voir les détails d'une Action
-   ✅ **Retour OS** : Bouton retour vers Objectif Stratégique
-   ✅ **Breadcrumb** : Navigation claire

---

## 🔍 **Tests à Effectuer**

### **Test 1 : Affichage de Base**

```bash
# 1. Aller sur `/piliers`
# 2. Cliquer sur l'œil d'un pilier
# 3. Cliquer sur l'œil d'un objectif stratégique
# 4. Cliquer sur l'œil d'un objectif spécifique
# 5. Vérifier que :
#    - Pas de champ "Date d'échéance"
#    - Taux d'avancement affiché en lecture seule
#    - 2 cartes des parents affichées
#    - Liste des actions visible
```

### **Test 2 : Création d'Objectif Spécifique**

```bash
# 1. Dans la vue détail OSP, cliquer sur "Créer une Action"
# 2. Vérifier que le modal s'ouvre
# 3. Vérifier qu'il n'y a PAS de champs :
#    - date_echeance
#    - taux_avancement
# 4. Remplir les champs requis
# 5. Soumettre le formulaire
# 6. Vérifier la notification toast
```

### **Test 3 : Modification d'Objectif Spécifique**

```bash
# 1. Dans la liste des OSP, cliquer sur le bouton "Modifier"
# 2. Vérifier que le modal s'ouvre avec les bonnes données
# 3. Vérifier qu'il n'y a PAS de champs :
#    - date_echeance
#    - taux_avancement
# 4. Modifier un champ
# 5. Soumettre le formulaire
# 6. Vérifier la notification toast
```

### **Test 4 : Suppression d'Objectif Spécifique**

```bash
# 1. Dans la liste des OSP, cliquer sur le bouton "Supprimer"
# 2. Vérifier que la confirmation s'affiche
# 3. Confirmer la suppression
# 4. Vérifier la notification toast
```

### **Test 5 : Navigation**

```bash
# 1. Cliquer sur le bouton "Voir" d'une action
# 2. Vérifier la navigation vers la vue Action
# 3. Utiliser le bouton retour
# 4. Vérifier le retour vers la vue OSP
```

---

## 🚨 **Vérifications Importantes**

### **1. Champs Supprimés :**

-   [ ] **`date_echeance`** : Absent de l'affichage et des formulaires
-   [ ] **`taux_avancement`** : Affiché en lecture seule, non modifiable

### **2. Permissions :**

-   [ ] **Création** : Bouton visible selon `canCreateObjectifSpecifique`
-   [ ] **Modification** : Bouton visible selon `canEditObjectifSpecifique`
-   [ ] **Suppression** : Bouton visible selon `canDeleteObjectifSpecifique`

### **3. Notifications :**

-   [ ] **Toast succès** : Affiché après opération réussie
-   [ ] **Toast échec** : Affiché après opération échouée
-   [ ] **Notifications DB** : Envoyées aux utilisateurs concernés

### **4. Responsivité :**

-   [ ] **2 cartes** : Sur même ligne (grand écran)
-   [ ] **2 cartes** : En colonne (petit écran)
-   [ ] **Tableau** : Responsive sur tous les écrans

---

## 🎯 **Résultat Attendu**

### **Vue Détail Objectif Spécifique :**

-   ✅ **Informations complètes** sans champs non désirés
-   ✅ **2 cartes des parents** avec responsivité parfaite
-   ✅ **Liste des actions** avec toutes les opérations CRUD
-   ✅ **Permissions respectées** pour toutes les actions
-   ✅ **Notifications** pour toutes les opérations
-   ✅ **Navigation fluide** entre les vues

### **Opérations CRUD :**

-   ✅ **Création** : Modal sans champs `date_echeance` et `taux_avancement`
-   ✅ **Modification** : Modal avec données pré-remplies
-   ✅ **Suppression** : Confirmation et notification
-   ✅ **Lecture** : Affichage complet des informations

---

## 📋 **Checklist de Test Final**

### **Affichage :**

-   [ ] Pas de champ date d'échéance
-   [ ] Taux d'avancement en lecture seule
-   [ ] 2 cartes des parents affichées
-   [ ] Liste des actions visible

### **CRUD :**

-   [ ] Création fonctionne (sans champs non désirés)
-   [ ] Modification fonctionne (données pré-remplies)
-   [ ] Suppression fonctionne (avec confirmation)
-   [ ] Permissions respectées

### **Navigation :**

-   [ ] Navigation vers Action fonctionne
-   [ ] Retour vers OS fonctionne
-   [ ] Breadcrumb clair

### **Responsivité :**

-   [ ] 2 cartes sur même ligne (grand écran)
-   [ ] 2 cartes en colonne (petit écran)
-   [ ] Tableau responsive
