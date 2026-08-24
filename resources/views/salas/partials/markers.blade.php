@foreach($salas as $sala)
    <div class="marker-item" 
         data-id="{{ $sala->id }}" 
         data-nome="{{ $sala->nome }}"
         style="position: absolute; left: {{ $sala->x }}%; top: {{ $sala->y }}%; transform: translate(-50%, -50%); cursor: pointer; z-index: 10; display: inline-flex; align-items: center; gap: 1px; background: rgba(30, 41, 59, 0.9); padding: 1px 2px; border-radius: 2px;">
        
        <!-- Ícone Micro (7x7) -->
        <svg width="7" height="7" viewBox="0 0 24 24" fill="#60a5fa" stroke="#ffffff" stroke-width="2" style="display: block; pointer-events: none;">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
        </svg>
        
        <!-- Texto Ultra Compacto (7px) -->
        <span style="color: #fff; font-size: 7px; line-height: 1; white-space: nowrap; font-weight: 600; pointer-events: none;">
            {{ $sala->nome }}
        </span>
    </div>
@endforeach