# Modal Pilier en Plein Écran

## Modifications Apportées

### 1. Changement de la Classe CSS du Modal

-   **Avant** : `modal-fullscreen-lg-down modal-xl modal-dialog-centered`
-   **Après** : `modal-fullscreen`
-   **Impact** : Le modal s'affiche maintenant en plein écran sur tous les appareils

### 2. Ajustement de la Hauteur du Body

-   **Avant** : `max-height: 80vh`
-   **Après** : `height: calc(100vh - 80px)`
-   **Impact** : Le contenu utilise toute la hauteur disponible de l'écran

### 3. Système de Grille à 2 Colonnes (1/3) Optimisé

Nouvelle disposition en grille avec :

-   **Colonne gauche (1/3)** : Carte de détail du pilier compacte et optimisée
-   **Colonne droite (2/3)** : Liste des objectifs stratégiques en tableau
-   **Responsive** : Adaptation automatique sur tous les écrans
-   **Espace optimisé** : Cartes compactes pour les niveaux hiérarchiques supérieurs

### 4. Hiérarchie de Couleurs Intelligente et Optimisée

Système de couleurs basé sur le niveau hiérarchique :

-   **Niveau 0 (Pilier)** : Couleur pure dans le header et la progression
-   **Niveau 1 (OS)** : `$pilier->getHierarchicalColor(1)` (85%) dans le header
-   **Niveau 2 (OSP)** : `$pilier->getHierarchicalColor(2)` (70%) dans les éléments
-   **Niveau 3 (Action)** : `$pilier->getHierarchicalColor(3)` (55%) dans les éléments
-   **Niveau 4 (SA)** : `$pilier->getHierarchicalColor(4)` (40%) dans les éléments

### 5. Carte de Détail du Pilier Optimisée

Nouvelle carte compacte à gauche avec :

-   **Header coloré** : Couleur du pilier uniquement dans le header
-   **Contenu compact** : Description, progression et statistiques optimisées
-   **Progression colorée** : Barre de progression avec la couleur du pilier
-   **Statistiques visuelles** : Bordures colorées pour les métriques
-   **Actions compactes** : Boutons optimisés pour l'espace

### 6. Vue Détaillée des Objectifs Stratégiques Optimisée

Quand on clique sur l'œil d'un OS :

-   **Colonne gauche (1/3)** :
    -   Carte du pilier parent compacte (couleur du pilier)
    -   Carte de l'OS compacte (couleur hiérarchique niveau 1)
-   **Colonne droite (2/3)** : Liste des objectifs spécifiques en tableau
-   **Espace optimisé** : Prêt pour les niveaux hiérarchiques supérieurs

### 7. Barre d'Outils Compacte et Responsive

Barre d'outils optimisée entre le header et le contenu avec :

-   **Recherche** : Champ de recherche compact pour filtrer les objectifs stratégiques
-   **Filtres** : Filtrage par statut avec design responsive
-   **Actions rapides** : Boutons Actualiser et Exporter avec texte conditionnel
-   **Création** : Bouton pour créer un nouvel objectif stratégique
-   **Responsive** : Texte masqué sur petits écrans pour économiser l'espace

### 8. Expérience Utilisateur Améliorée

-   **Cartes compactes** : Hauteur optimisée pour une meilleure lisibilité
-   **Couleurs dans les headers** : Meilleure hiérarchie visuelle
-   **Progression colorée** : Barres de progression avec couleurs du pilier
-   **Animations fluides** : Transitions et effets de survol
-   **Responsive design** : Adaptation optimale à tous les écrans
-   **Lisibilité améliorée** : Typographie et contrastes optimisés

### 9. Styles CSS Personnalisés et Optimisés

-   **Modal plein écran** : Suppression des bordures arrondies
-   **Couleurs hiérarchiques** : Application automatique dans les headers et progressions
-   **Animations** : Transitions fluides et effets de survol
-   **Responsive** : Adaptation optimale à tous les écrans
-   **Performance** : CSS optimisé pour une meilleure fluidité

## Fonctions JavaScript Ajoutées

### `refreshPilierData()`

Actualise les données du pilier via Livewire

### `exportPilierData()`

Prépare l'export des données (à développer selon les besoins)

### `togglePilierView()`

Change le mode d'affichage (à développer selon les besoins)

### `highlightSearchTerm(row, searchTerm)`

Met en surbrillance le texte recherché dans les résultats

## Utilisation

### Vue Principale du Pilier

1. **Ouvrir le modal** : Cliquer sur l'icône œil d'un pilier
2. **Colonne gauche** : Voir les détails du pilier avec sa couleur dans le header
3. **Colonne droite** : Consulter la liste des objectifs stratégiques
4. **Rechercher** : Utiliser le champ de recherche compact pour filtrer les OS
5. **Filtrer** : Sélectionner un statut dans le menu déroulant

### Vue Détaillée d'un Objectif Stratégique

1. **Cliquer sur l'œil** d'un objectif stratégique dans la liste
2. **Colonne gauche** :

-   Voir le contexte du pilier parent (carte compacte)
-   Consulter les détails de l'OS (carte compacte)

3. **Colonne droite** : Liste des objectifs spécifiques liés
4. **Navigation** : Utiliser le breadcrumb pour revenir au pilier

### Niveaux Hiérarchiques Supérieurs

-   **Prêt pour l'extension** : L'espace est optimisé pour ajouter plus de cartes
-   **Couleurs cohérentes** : Chaque niveau utilise sa couleur hiérarchique
-   **Navigation intuitive** : Contexte clair à chaque niveau

## Hiérarchie des Couleurs

```
Pilier (Niveau 0)     → Couleur pure (100%) - Header et progression
├── OS (Niveau 1)     → 85% d'opacité - Header de la carte OS
├── OSP (Niveau 2)    → 70% d'opacité - Éléments visuels
├── Action (Niveau 3) → 55% d'opacité - Éléments visuels
└── SA (Niveau 4)     → 40% d'opacité - Éléments visuels
```

## Avantages de l'Optimisation

1. **Espace optimisé** : Cartes compactes pour une meilleure utilisation de l'écran
2. **Hiérarchie claire** : Couleurs dans les headers pour une meilleure organisation
3. **Navigation intuitive** : Contexte visuel à chaque niveau hiérarchique
4. **Responsive design** : Adaptation optimale à tous les écrans
5. **Performance améliorée** : CSS optimisé et animations fluides
6. **Extensibilité** : Prêt pour les niveaux hiérarchiques supérieurs
7. **Expérience utilisateur** : Interface moderne et intuitive

## Compatibilité

-   **Bootstrap 5** : Utilise les classes `modal-fullscreen`
-   **Livewire** : Compatible avec les composants Livewire existants
-   **Responsive** : S'adapte à tous les écrans (mobile, tablette, desktop)
-   **Navigateurs** : Compatible avec tous les navigateurs modernes

## Développements Futurs

-   [ ] Vue détaillée des actions et sous-actions
-   [ ] Graphiques de progression en temps réel
-   [ ] Export complet (Excel, PDF)
-   [ ] Changement de vue (tableau, cartes, graphiques)
-   [ ] Filtres avancés (par date, propriétaire, etc.)
-   [ ] Tri des colonnes du tableau
-   [ ] Pagination pour les grands ensembles de données
-   [ ] Navigation entre niveaux hiérarchiques
-   [ ] Historique de navigation
