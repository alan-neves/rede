<style>
    /* Estilo do container relativo para a tooltip se posicionar */
    .marker-sala {
        position: relative;
    }
    
    /* Configuração da Tooltip estilizada */
    .marker-sala::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 125%; /* Aparece logo acima do marcador */
        left: 50%;
        transform: translateX(-50%);
        background-color: #0f172a; /* Slate 900 - escuro e moderno */
        color: #ffffff;
        padding: 6px 12px;
        border-radius: 6px;
        
        /* Tamanho e formatação de texto fixos para Web */
        font-size: 16px;
        font-weight: 500;
        line-height: 1.4;
        text-align: center;
        
        /* Controle de tamanho e quebra de linha */
        white-space: normal;
        max-width: 220px;
        width: max-content;
        
        /* Animação suave ao passar o mouse */
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
        pointer-events: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        z-index: 30;
    }

    /* Triângulo indicador (setinha virada para baixo) */
    .marker-sala::before {
        content: '';
        position: absolute;
        bottom: 110%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 5px 5px 0 5px;
        border-style: solid;
        border-color: #0f172a transparent transparent transparent;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
        pointer-events: none;
        z-index: 30;
    }

    /* Exibe o balão e a setinha no hover */
    .marker-sala:hover::after,
    .marker-sala:hover::before {
        opacity: 1;
        visibility: visible;
    }
</style>

@foreach($salas as $sala)
    @php
        $size = $sala->fontsize ?? 12; 
    @endphp

    <div class="marker-item marker-sala" 
         data-id="{{ $sala->id }}" 
         data-nome="{{ $sala->nome }}"
         data-tooltip="{{ $sala->descricao ?? $sala->nome }}"
         style="position: absolute; left: {{ $sala->x }}%; top: {{ $sala->y }}%; transform: translate(-50%, -50%); cursor: pointer; z-index: 10; display: inline-flex; align-items: center; gap: 2px;">
        
        <!-- Ícone -->
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="#2563eb" stroke="#ffffff" stroke-width="2" style="display: block; pointer-events: none;">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5-2.5z"/>
        </svg>
        
        <!-- Texto -->
        <span style="color: #1e293b; font-size: {{ $size }}px; line-height: 1; white-space: nowrap; font-weight: 700; pointer-events: none; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;">
            {{ $sala->nome }}
        </span>
    </div>
@endforeach