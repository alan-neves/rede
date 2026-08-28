@foreach($markers as $marker)
    @php
        $size = $marker->fontsize ?? 12; 
        $labelPosition = $marker->label_position ?? 'right';
        $nomeFormatado = optional(optional($marker->patchPanel)->rack)->nome . '-' . optional($marker->patchPanel)->nome . '-' . $marker->porta;
        
        $tooltipText = !empty($marker->comentario) ? $nomeFormatado . ' — ' . $marker->comentario : $nomeFormatado;

        // Ajusta a direção e orientação do texto sem margens negativas que causem sobreposição
        switch($labelPosition) {
            case 'left':
                $flexStyles = 'flex-direction: row-reverse;';
                $textStyles = '';
                break;
            case 'top':
                $flexStyles = 'flex-direction: column-reverse;';
                $textStyles = 'writing-mode: vertical-rl; transform: rotate(180deg);';
                break;
            case 'bottom':
                $flexStyles = 'flex-direction: column;';
                $textStyles = 'writing-mode: vertical-rl; transform: rotate(180deg);';
                break;
            default: // right
                $flexStyles = 'flex-direction: row;';
                $textStyles = '';
                break;
        }
    @endphp

    <div class="marker-item marker-ponto" 
         data-id="{{ $marker->id }}" 
         data-nome="{{ $nomeFormatado }}"
         data-tipo="{{ $marker->tipo_porta_id ?? '' }}"
         data-comentario="{{ $marker->comentario ?? '' }}"
         data-tamanho="{{ $marker->tamanho ?? '' }}"
         data-fontsize="{{ $size }}"
         data-label-position="{{ $labelPosition }}"
         data-labelposition="{{ $labelPosition }}"
         data-tooltip="{{ $tooltipText }}"
         style="position: absolute; left: {{ $marker->x }}%; top: {{ $marker->y }}%; transform: translate(-50%, -50%); cursor: pointer; z-index: 10; display: inline-flex; align-items: center; justify-content: center; gap: 0px; {{ $flexStyles }}">
        
        <!-- Ícone (Triângulo Vermelho) -->
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 100 100" style="display: block; pointer-events: none; flex-shrink: 0;">
            <polygon points="50,15 90,85 10,85" fill="#ef4444" stroke="#ffffff" stroke-width="8" />
        </svg>
        
        <!-- Texto alinhado sem sobrepor o ícone -->
        <span style="color: #1e293b; font-size: {{ $size }}px; line-height: 1; margin: 0; padding: 0; white-space: nowrap; font-weight: 700; pointer-events: none; text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff; {{ $textStyles }}">
            {{ $nomeFormatado }}
        </span>
    </div>
@endforeach