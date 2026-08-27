<style>
    /* Garante o posicionamento relativo para os marcadores de ponto */
    .marker-ponto {
        position: relative;
    }
    
    /* Balão da Tooltip */
    .marker-ponto::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        background-color: #0f172a;
        color: #ffffff;
        padding: 6px 12px;
        border-radius: 6px;
        
        /* Formatação de texto padrão web */
        font-size: 12px;
        font-weight: 500;
        line-height: 1.4;
        text-align: center;
        
        /* Limites de largura para comentários longos */
        white-space: normal;
        max-width: 240px;
        width: max-content;
        
        /* Transições de visibilidade */
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
        pointer-events: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        z-index: 30;
    }

    /* Setinha virada para baixo apontando para o ícone */
    .marker-ponto::before {
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

    /* Exibição no Hover */
    .marker-ponto:hover::after,
    .marker-ponto:hover::before {
        opacity: 1;
        visibility: visible;
    }
</style>

@foreach($markers as $marker)
    @php
        $size = $marker->fontsize ?? 12; 
        $nomeFormatado = optional(optional($marker->patchPanel)->rack)->nome . '-' . optional($marker->patchPanel)->nome . '-' . $marker->porta;
        
        // Define o texto da tooltip: mostra o comentário se existir ou usa o nome formatado do ponto
        $tooltipText = !empty($marker->comentario) ? $nomeFormatado . ' — ' . $marker->comentario : $nomeFormatado;
    @endphp

    <div class="marker-item marker-ponto" 
         data-id="{{ $marker->id }}" 
         data-nome="{{ $nomeFormatado }}"
         data-tipo="{{ $marker->tipo_porta_id ?? '' }}"
         data-comentario="{{ $marker->comentario ?? '' }}"
         data-tamanho="{{ $marker->tamanho ?? '' }}"
         data-fontsize="{{ $size }}"
         data-tooltip="{{ $tooltipText }}"
         style="position: absolute; left: {{ $marker->x }}%; top: {{ $marker->y }}%; transform: translate(-50%, -50%); cursor: pointer; z-index: 10; display: inline-flex; align-items: center; gap: 2px;">
        
        <!-- Ícone (Triângulo Vermelho) -->
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 100 100" style="display: block; pointer-events: none;">
            <polygon points="50,15 90,85 10,85" fill="#ef4444" stroke="#ffffff" stroke-width="8" />
        </svg>
        
        <!-- Texto com o tamanho dinâmico vindo do banco -->
        <span style="color: #1e293b; font-size: {{ $size }}px; line-height: 1; white-space: nowrap; font-weight: 700; pointer-events: none; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;">
            {{ $nomeFormatado }}
        </span>
    </div>
@endforeach