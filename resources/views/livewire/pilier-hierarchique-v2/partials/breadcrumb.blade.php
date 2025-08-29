<!-- Breadcrumb de navigation compact et responsive -->
<div class="breadcrumb-container bg-gradient-light border-bottom p-3">
    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <ol class="breadcrumb mb-0 flex-wrap">
            @foreach($breadcrumb as $index => $item)
                @if($index > 0)
                    <li class="breadcrumb-separator">
                        <i class="fas fa-chevron-right text-muted mx-2 d-none d-sm-inline"></i>
                        <i class="fas fa-chevron-down text-muted mx-1 d-inline d-sm-none"></i>
                    </li>
                @endif
                
                <li class="breadcrumb-item {{ $index === count($breadcrumb) - 1 ? 'active' : '' }}" 
                    aria-current="{{ $index === count($breadcrumb) - 1 ? 'page' : '' }}">
                    
                    @if($item['action'] && $index < count($breadcrumb) - 1)
                        <!-- Niveaux parents : afficher seulement le code -->
                        <button type="button" 
                                class="btn btn-link btn-sm p-0 text-decoration-none breadcrumb-link" 
                                wire:click="{{ $item['action'] }}"
                                title="Retour vers {{ $item['label'] }}">
                            <span class="breadcrumb-text">{{ $item['code'] ?? $item['label'] }}</span>
                        </button>
                    @else
                        <!-- Niveau actuel : afficher code + libellé -->
                        <span class="breadcrumb-text fw-bold {{ $index === count($breadcrumb) - 1 ? 'text-primary' : '' }}">
                            @if($index === count($breadcrumb) - 1)
                                {{ $item['code'] ?? '' }} {{ $item['label'] }}
                            @else
                                {{ $item['code'] ?? $item['label'] }}
                            @endif
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
        
        <!-- Indicateur de niveau actuel pour mobile -->
        <div class="breadcrumb-mobile-indicator d-block d-sm-none mt-2">
            <small class="text-muted">
                <i class="fas fa-layer-group me-1"></i>
                Niveau {{ count($breadcrumb) }} sur {{ count($breadcrumb) }}
            </small>
        </div>
    </nav>
</div>
