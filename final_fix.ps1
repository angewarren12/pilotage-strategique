# Script PowerShell final pour corriger tous les attributs style malformés
$filePath = "resources/views/livewire/pilier-hierarchique-modal.blade.php"
$content = Get-Content $filePath -Raw

# Corriger tous les attributs style malformés restants
$content = $content -replace 'style="color: {{ \$pilier->getHierarchicalColor\(4\) }};"', 'style="color: {{ $pilier->getHierarchicalColor(4) }};"'
$content = $content -replace 'style="color: {{ \$pilier->getHierarchicalColor\(4\) }};"', 'style="color: {{ $pilier->getHierarchicalColor(4) }};"'
$content = $content -replace 'style="color: {{ \$pilier->getHierarchicalColor\(4\) }};"', 'style="color: {{ $pilier->getHierarchicalColor(4) }};"'
$content = $content -replace 'style="color: {{ \$pilier->getHierarchicalColor\(4\) }};"', 'style="color: {{ $pilier->getHierarchicalColor(4) }};"'

# Sauvegarder le fichier corrigé
Set-Content $filePath $content -Encoding UTF8

Write-Host "Tous les attributs style malformés ont été corrigés!"



