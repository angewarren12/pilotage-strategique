<?php

// Script pour corriger tous les overlays des modals
$file_path = 'resources/views/livewire/pilier-hierarchique-v2/components/modals.blade.php';

if (!file_exists($file_path)) {
    echo "Fichier non trouvé: $file_path\n";
    exit(1);
}

// Lire le contenu du fichier
$content = file_get_contents($file_path);

// Remplacer tous les overlays manuels par des commentaires
$content = str_replace(
    '<div class="modal-backdrop fade show"></div>',
    '<!-- Overlay supprimé - Bootstrap gère automatiquement l\'arrière-plan sombre -->',
    $content
);

// Ajouter data-bs-backdrop="true" aux modals qui n'en ont pas
$content = str_replace(
    '<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog">',
    '<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog" data-bs-backdrop="true">',
    $content
);

// Écrire le contenu modifié
if (file_put_contents($file_path, $content)) {
    echo "✅ Tous les overlays des modals ont été corrigés avec succès !\n";
    echo "📁 Fichier modifié: $file_path\n";
} else {
    echo "❌ Erreur lors de l'écriture du fichier\n";
}

echo "\n🔧 Modifications effectuées:\n";
echo "- Supprimé tous les overlays manuels <div class=\"modal-backdrop fade show\"></div>\n";
echo "- Ajouté data-bs-backdrop=\"true\" aux modals\n";
echo "- Remplacé par des commentaires explicatifs\n";

?>
