@foreach($markers as $marker)
    <div class="marker-item marker-ponto" 
         data-id="{{ $marker->id }}" 
         data-nome="{{ optional(optional($marker->patchPanel)->rack)->nome }}{{ $marker->porta }}"
         style="position: absolute; left: {{ $marker->x }}%; top: {{ $marker->y }}%; transform: translate(-50%, -50%); cursor: pointer; z-index: 10; display: inline-flex; align-items: center; gap: 1px; background: rgba(0, 0, 0, 0.85); padding: 1px 2px; border-radius: 2px;">
        
        <!-- Ícone Micro (7x7) -->
        <svg width="7" height="7" viewBox="0 0 100 100" style="display: block; pointer-events: none;">
            <polygon points="50,15 90,85 10,85" fill="#ef4444" stroke="#ffffff" stroke-width="8" />
        </svg>
        
        <!-- Texto Ultra Compacto (7px) -->
        <span style="color: #fff; font-size: 7px; line-height: 1; white-space: nowrap; font-weight: 600; pointer-events: none;">
            {{ optional(optional($marker->patchPanel)->rack)->nome }}{{ $marker->porta }}
        </span>
    </div>
@endforeach