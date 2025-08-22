<!-- Breadcrumb de navigation -->
<div class="breadcrumb-container bg-light border-bottom p-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            @foreach($breadcrumb as $index => $item)
                @if($index > 0)
                    <li class="breadcrumb-item">
                        <i class="fas fa-chevron-right text-muted mx-2"></i>
                    </li>
                @endif
                
                <li class="breadcrumb-item {{ $index === count($breadcrumb) - 1 ? 'active' : '' }}" 
                    aria-current="{{ $index === count($breadcrumb) - 1 ? 'page' : '' }}">
                    
                    @if($item['action'] && $index < count($breadcrumb) - 1)
                        <button type="button" 
                                class="btn btn-link btn-sm p-0 text-decoration-none" 
                                wire:click="{{ $item['action'] }}"
                                title="Retour vers {{ $item['label'] }}">
                            {{ $item['label'] }}
                        </button>
                    @else
                        <span class="fw-bold">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
</div>
