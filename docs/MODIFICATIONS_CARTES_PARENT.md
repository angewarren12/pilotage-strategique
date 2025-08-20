# Modifications des Cartes Parent - Design Hiérarchique

## 🎯 **Objectif :**

Transformer les cartes parentes pour qu'elles aient un design élégant avec uniquement des icônes pour les boutons et un contenu simplifié.

## 📋 **Règles à Appliquer :**

### 1. **Carte Principale (Élément détaillé) :**

-   ✅ **Affiche le nombre d'enfants**
-   ✅ **Boutons avec texte + icône**
-   ✅ **Toutes les informations détaillées**

### 2. **Cartes Parentes :**

-   ❌ **N'affichent PAS le nombre d'enfants**
-   ✅ **Boutons uniquement avec icônes** (🖊️ et 🗑️)
-   ✅ **Contenu simplifié** : libellé, codification, description, progression

## 🎨 **Design des Boutons Parent :**

### **Classes CSS à ajouter :**

```css
.parent-action-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.parent-action-btn.edit {
    border-color: #ffc107;
    color: #ffc107;
}

.parent-action-btn.delete {
    border-color: #dc3545;
    color: #dc3545;
}
```

### **HTML des Boutons Parent :**

```html
<!-- Boutons d'action pour cartes parentes (icônes uniquement) -->
<div class="d-flex gap-2 mt-3 justify-content-center">
    <button
        type="button"
        class="btn parent-action-btn edit"
        wire:click="showEditObjectifStrategiqueForm({{ $selectedObjectifStrategique->id }})"
        title="Modifier"
    >
        <i class="fas fa-edit"></i>
    </button>
    <button
        type="button"
        class="btn parent-action-btn delete"
        wire:click="deleteObjectifStrategique({{ $selectedObjectifStrategique->id }})"
        onclick="if(!confirm('Êtes-vous sûr de vouloir supprimer cet objectif stratégique ?')) return false;"
        title="Supprimer"
    >
        <i class="fas fa-trash"></i>
    </button>
</div>
```

## 🔧 **Modifications à Apporter :**

### **1. Vue Principale du Pilier :**

-   **Carte Pilier** : Garder le nombre d'OS et boutons avec texte
-   **Carte OS** : Transformer en carte parente (pas de nombre d'enfants, boutons icônes)

### **2. Vue Détails Objectif Stratégique :**

-   **Carte Pilier** : Carte parente
-   **Carte OS** : Carte principale avec nombre d'OSP et Actions
-   **Carte OSP** : Transformer en carte parente

### **3. Vue Détails Objectif Spécifique :**

-   **Carte Pilier** : Carte parente
-   **Carte OS** : Carte parente
-   **Carte OSP** : Carte principale avec nombre d'Actions
-   **Carte Action** : Transformer en carte parente

### **4. Vue Détails Action :**

-   **Carte Pilier** : Carte parente
-   **Carte OS** : Carte parente
-   **Carte OSP** : Carte parente
-   **Carte Action** : Carte principale avec nombre de Sous-Actions

### **5. Vue Détails Sous-Action :**

-   **Carte Pilier** : Carte parente
-   **Carte OS** : Carte parente
-   **Carte OSP** : Carte parente
-   **Carte Action** : Carte parente
-   **Carte Sous-Action** : Carte principale

## 📝 **Structure des Cartes Parentes :**

```html
<div class="card mb-3 border-0 shadow-sm hierarchy-card level-X parent-card">
    <div class="hierarchy-indicator">X</div>
    <div class="hierarchy-connector level-X"></div>

    <div class="card-header">
        <h6 class="card-title">Titre Parent</h6>
        <small class="card-subtitle">Codification</small>
    </div>

    <div class="card-body">
        <!-- Description si présente -->
        @if($description)
        <p class="text-muted mb-3">{{ Str::limit($description, 80) }}</p>
        @endif

        <!-- Barre de progression -->
        <div class="progress mb-3">
            <div class="progress-bar" style="width: {{ $progression }}%"></div>
        </div>

        <!-- Pourcentage -->
        <div class="text-center mb-3">
            <small class="text-muted"
                >{{ number_format($progression, 1) }}% de progression</small
            >
        </div>

        <!-- Boutons d'action (icônes uniquement) -->
        <div class="d-flex gap-2 justify-content-center">
            <button class="btn parent-action-btn edit" title="Modifier">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn parent-action-btn delete" title="Supprimer">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</div>
```

## 🚀 **Ordre d'Implémentation :**

1. ✅ **CSS des boutons parent** (déjà ajouté)
2. 🔄 **Modifier la carte OS dans la vue principale**
3. 🔄 **Modifier la carte OSP dans la vue OS**
4. 🔄 **Modifier la carte Action dans la vue OSP**
5. 🔄 **Modifier la carte Sous-Action dans la vue Action**
6. 🔄 **Vérifier la cohérence sur tous les niveaux**

## 🎯 **Résultat Final :**

-   **Hiérarchie visuelle claire** avec marges progressives
-   **Boutons parent élégants** avec animations et effets
-   **Contenu simplifié** pour les cartes parentes
-   **Nombre d'enfants** uniquement sur la carte principale
-   **Design moderne** avec gradients et effets de transparence

