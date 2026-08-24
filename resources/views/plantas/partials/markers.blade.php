@foreach($markers as $marker)
    <div class="marker-item" 
         data-id="{{ $marker->id }}" 
         data-nome="{{ optional(optional($marker->patchPanel)->rack)->nome }}{{ $marker->porta }}"
         style="position: absolute; left: {{ $marker->x }}%; top: {{ $marker->y }}%; transform: translate(-50%, -100%); cursor: pointer; z-index: 10;">
        <svg width="20" height="20" viewBox="0 0 100 100" style="display: block; margin: 0 auto; pointer-events: none;">
            <polygon points="50,15 90,85 10,85" fill="#ef4444" stroke="#991b1b" stroke-width="5" />
        </svg>
        <span style="background: #000; color: #fff; font-size: 11px; padding: 2px 4px; white-space: nowrap; pointer-events: none;">
            {{ optional(optional($marker->patchPanel)->rack)->nome }}{{ $marker->porta }}
        </span>
    </div>
@endforeach