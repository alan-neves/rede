<!-- Contêiner ocupando 100% da largura da página -->
<div id="svg-container" style="position: relative; width: 100%; display: block; cursor: crosshair;">

    <!-- Imagem SVG ajustada para 100% de largura -->
    <img id="svg-image" src="{{ '/plantas/' . $planta->predio_id . '/' . $planta->id }}" style="width: 100%; height: auto; display: block;" alt="Planta Baixa">

    <!-- Renderização dos Marcadores Salvos -->
    @foreach($markers as $marker)
        <div style="position: absolute; left: {{ $marker->x }}%; top: {{ $marker->y }}%; transform: translate(-50%, -100%); pointer-events: none;">
            <svg width="20" height="20" viewBox="0 0 100 100" style="display: block; margin: 0 auto;">
                <polygon points="50,15 90,85 10,85" fill="#ef4444" stroke="#991b1b" stroke-width="5" />
            </svg>
            <span style="background: #000; color: #fff; font-size: 11px; padding: 2px 4px; white-space: nowrap;">
                {{ optional(optional($marker->patchPanel)->rack)->nome }}{{ $marker->porta }}
            </span>
        </div>
    @endforeach

    <!-- Formulário Pop-up Nativo (Aparece no local do clique) -->
    <div id="popoverForm" style="display: none; position: absolute; background: #fff; border: 1px solid #000; padding: 10px; z-index: 1000;">

    <form action="/plantas/{{ $planta->id }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Inputs Ocultos com IDs ÚNICOS para o JavaScript -->
        <input type="hidden" name="x" id="inputX">
        <input type="hidden" name="y" id="inputY">
        <input type="hidden" name="planta_id" value="{{ $planta->id }}">

        <label for="patch_panel_sala_id" style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 6px;">
            Selecione o Ponto:
        </label>

        <select name="patch_panel_sala_id" id="patch_panel_sala_id" required style="width: 100%; padding: 5px; margin-bottom: 10px;">
            <option value="">-- Selecione --</option>
            @foreach($pontosSemMarcacao as $ponto)
                <option value="{{ $ponto->id }}">
                    {{ optional(optional($ponto->patchPanel)->rack)->nome }}{{ $ponto->porta }} 
                    @if($ponto->sala)
                        ({{ $ponto->sala->nome }})
                    @endif
                </option>
            @endforeach
        </select>

        @if($pontosSemMarcacao->isEmpty())
            <p style="color: red; font-size: 11px; margin-top: 0;">Nenhum ponto pendente de marcação neste prédio.</p>
        @endif

        <div style="display: flex; justify-content: space-between;">
            <button type="submit" @if($pontosSemMarcacao->isEmpty()) disabled @endif style="cursor: pointer;">Salvar Ponto</button>
            <button type="button" onclick="closeForm()" style="cursor: pointer;">Cancelar</button>
        </div>
    </form>
    </div>

</div>

<script>
    const svgImage = document.getElementById('svg-image');
    const popoverForm = document.getElementById('popoverForm');

    svgImage.addEventListener('click', function (e) {
        const rect = svgImage.getBoundingClientRect();

        // Posição percentual
        const xPercent = ((e.clientX - rect.left) / rect.width) * 100;
        const yPercent = ((e.clientY - rect.top) / rect.height) * 100;

        // Posição em pixels para abrir o popover
        const clickX = e.clientX - rect.left;
        const clickY = e.clientY - rect.top;

        // GARANTIA: Seleciona explicitamente os inputs pelo id exato
        const inputX = document.getElementById('inputX');
        const inputY = document.getElementById('inputY');

        if (inputX && inputY) {
            inputX.value = xPercent.toFixed(2);
            inputY.value = yPercent.toFixed(2);
        }

        // Ajuste para não cortar a caixinha na borda direita
        const formWidth = 280;
        let leftPos = clickX;
        if (clickX + formWidth > rect.width) {
            leftPos = clickX - formWidth;
        }

        popoverForm.style.left = leftPos + 'px';
        popoverForm.style.top = clickY + 'px';
        popoverForm.style.display = 'block';

        const selectPonto = document.getElementById('patch_panel_sala_id');
        if (selectPonto) {
            setTimeout(() => selectPonto.focus(), 50);
        }
    });

    function closeForm() {
        popoverForm.style.display = 'none';
        const selectPonto = document.getElementById('patch_panel_sala_id');
        if (selectPonto) {
            selectPonto.value = '';
        }
    }
</script>