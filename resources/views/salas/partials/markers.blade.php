@foreach($salas as $sala)
    @php
        // Usa o valor do banco de dados ou 12px como padrão
        $size = $sala->fontsize ?? 12; 
    @endphp

    <div class="marker-item marker-sala" 
         data-id="{{ $sala->id }}" 
         data-nome="{{ $sala->nome }}"
         style="position: absolute; left: {{ $sala->x }}%; top: {{ $sala->y }}%; transform: translate(-50%, -50%); cursor: pointer; z-index: 10; display: inline-flex; align-items: center; gap: 2px;">
        
        <!-- Ícone ajustado dinamicamente com o tamanho da fonte -->
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="#2563eb" stroke="#ffffff" stroke-width="2" style="display: block; pointer-events: none;">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
        </svg>
        
        <!-- Texto com o font-size vindo direto do banco de dados -->
        <span style="color: #1e293b; font-size: {{ $size }}px; line-height: 1; white-space: nowrap; font-weight: 700; pointer-events: none; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;">
            {{ $sala->nome }}
        </span>
    </div>
@endforeach