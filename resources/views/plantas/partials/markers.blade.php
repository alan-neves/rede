@foreach($markers as $marker)
    @php
        // Usa o valor da coluna fontsize do banco de dados ou 12px como fallback
        $size = $marker->fontsize ?? 12; 
        $nomeFormatado = optional(optional($marker->patchPanel)->rack)->nome . '-' . optional($marker->patchPanel)->nome . '-' . $marker->porta;
    @endphp

    <div class="marker-item marker-ponto" 
         data-id="{{ $marker->id }}" 
         data-nome="{{ $nomeFormatado }}"
         data-tipo="{{ $marker->tipo_porta_id ?? '' }}"
         data-comentario="{{ $marker->comentario ?? '' }}"
         data-tamanho="{{ $marker->tamanho ?? '' }}"
         data-fontsize="{{ $size }}"
         style="position: absolute; left: {{ $marker->x }}%; top: {{ $marker->y }}%; transform: translate(-50%, -50%); cursor: pointer; z-index: 10; display: inline-flex; align-items: center; gap: 2px;">
        
        <!-- Ícone (Triângulo Vermelho) ajustado dinamicamente -->
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 100 100" style="display: block; pointer-events: none;">
            <polygon points="50,15 90,85 10,85" fill="#ef4444" stroke="#ffffff" stroke-width="8" />
        </svg>
        
        <!-- Texto com o tamanho dinâmico vindo do banco -->
        <span style="color: #1e293b; font-size: {{ $size }}px; line-height: 1; white-space: nowrap; font-weight: 700; pointer-events: none; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;">
            {{ $nomeFormatado }}
        </span>
    </div>
@endforeach