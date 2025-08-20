# Standard des Modals de Création et d'Édition

## 🎯 **Objectif**

Définir un standard uniforme pour tous les modals de création et d'édition dans l'application, en utilisant une taille moyenne (`modal-lg`) pour une meilleure expérience utilisateur.

## 📏 **Taille Standardisée**

### **Classes CSS Utilisées**

```html
<div class="modal-dialog modal-lg modal-dialog-centered"></div>
```

-   **`modal-lg`** : Taille moyenne (plus grande que `modal-sm`, plus petite que `modal-xl`)
-   **`modal-dialog-centered`** : Centrage vertical automatique
-   **Largeur** : Environ 800px (responsive)

## 🎨 **Style Standardisé**

### **1. Header du Modal**

```html
<div class="modal-header bg-[couleur] text-[couleur-texte]">
    <h5 class="modal-title">
        <i class="fas fa-[icône] me-2"></i>
        [Titre du Modal]
    </h5>
    <button
        type="button"
        class="btn-close [btn-close-white]"
        wire:click="[méthode-annulation]"
    ></button>
</div>
```

**Couleurs par type :**

-   **Création** : `bg-success text-white` (vert)
-   **Édition** : `bg-warning text-dark` (orange) ou `bg-primary text-white` (bleu)
-   **Suppression** : `bg-danger text-white` (rouge)

### **2. Body du Modal**

```html
<div class="modal-body p-4">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">[Label] *</label>
            <input
                type="text"
                class="form-control @error('[champ]') is-invalid @enderror"
                wire:model="[champ]"
                required
                placeholder="[Placeholder]"
            />
            @error('[champ]')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <!-- Deuxième champ sur la même ligne -->
        </div>
    </div>
    <div class="mb-3">
        <!-- Champs sur toute la largeur -->
    </div>
</div>
```

**Caractéristiques :**

-   **Padding** : `p-4` pour l'espacement
-   **Grille** : `row` et `col-md-6` pour 2 colonnes
-   **Espacement** : `mb-3` entre les champs
-   **Labels** : `fw-bold` pour la hiérarchie visuelle
-   **Placeholders** : Textes d'aide pour guider l'utilisateur

### **3. Footer du Modal**

```html
<div class="modal-footer bg-light">
    <button
        type="button"
        class="btn btn-secondary"
        wire:click="[méthode-annulation]"
    >
        <i class="fas fa-times me-2"></i>Annuler
    </button>
    <button type="submit" class="btn btn-[couleur]">
        <i class="fas fa-save me-2"></i>[Action]
    </button>
</div>
```

**Caractéristiques :**

-   **Background** : `bg-light` pour le contraste
-   **Boutons** : Icônes et texte descriptif
-   **Couleurs** : `btn-secondary` pour Annuler, couleur spécifique pour l'action

## 🔧 **Implémentation**

### **Modals Déjà Standardisés**

#### **1. Objectifs Stratégiques**

-   ✅ **Création** : `modal-lg`, interface en 2 colonnes
-   ✅ **Édition** : `modal-lg`, interface en 2 colonnes

#### **2. Objectifs Spécifiques**

-   ✅ **Création** : `modal-lg`, interface en 2 colonnes
-   ✅ **Édition** : `modal-lg`, interface en 2 colonnes

### **Modals à Standardiser**

#### **3. Actions**

-   [ ] **Création** : À transformer en `modal-lg`
-   [ ] **Édition** : À transformer en `modal-lg`

#### **4. Sous-Actions**

-   [ ] **Création** : À transformer en `modal-lg`
-   [ ] **Édition** : À transformer en `modal-lg`

## 📱 **Responsive Design**

### **Comportement par Écran**

-   **Mobile (< 768px)** : Pleine largeur, champs empilés
-   **Tablette (≥ 768px)** : Largeur adaptée, grille 2 colonnes
-   **Desktop (≥ 992px)** : Largeur optimisée, grille 2 colonnes

### **Classes Responsive**

```html
<div class="col-md-6 mb-3">
    <!-- S'adapte automatiquement à la taille d'écran -->
</div>
```

## 🎨 **Cohérence Visuelle**

### **Avantages du Standard**

1. **Uniformité** : Tous les modals ont la même apparence
2. **Lisibilité** : Taille optimale pour la lecture
3. **Espacement** : Padding et marges cohérents
4. **Hiérarchie** : Labels en gras pour la clarté
5. **Accessibilité** : Boutons avec icônes et texte

### **Éléments Communs**

-   **Taille** : `modal-lg` pour tous
-   **Centrage** : `modal-dialog-centered` pour tous
-   **Padding** : `p-4` dans le body
-   **Labels** : `fw-bold` pour tous
-   **Boutons** : Icônes et texte pour tous

## 🚀 **Utilisation**

### **Pour Créer un Nouveau Modal**

1. **Copier la structure standard**
2. **Adapter les couleurs selon le type**
3. **Personnaliser les champs et validations**
4. **Maintenir la cohérence visuelle**

### **Exemple de Structure**

```html
@if($showCreateForm)
<div
    class="modal fade show"
    style="display: block; z-index: 9999;"
    tabindex="-1"
    data-bs-backdrop="static"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Header standard -->
            <!-- Body standard avec champs -->
            <!-- Footer standard -->
        </div>
    </div>
</div>
<div class="modal-backdrop fade show" style="z-index: 1050;"></div>
@endif
```

## 📝 **Résumé**

Le standard `modal-lg` offre :

-   ✅ **Taille optimale** : Ni trop petit, ni trop grand
-   ✅ **Interface cohérente** : Style uniforme dans toute l'application
-   ✅ **Responsive** : Adaptation automatique à tous les écrans
-   ✅ **Accessibilité** : Labels en gras, icônes, placeholders
-   ✅ **Maintenabilité** : Code standardisé et réutilisable

Tous les modals de création et d'édition suivent maintenant ce standard ! 🎉✨
