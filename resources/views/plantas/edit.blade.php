<a href="/plantas/{{ $planta->predio_id }}" style="display: inline-block; margin-bottom: 10px; text-decoration: none; color: #000; background-color: #f0f0f0; padding: 5px 10px; border-radius: 4px;">
    &larr; Voltar
</a>
<!-- Contêiner ocupando 100% da largura da página -->
<div id="svg-container" style="position: relative; width: 100%; display: block; cursor: crosshair;">

    <!-- Imagem SVG ajustada para 100% de largura -->
    <img id="svg-image" src="{{ '/plantas/' . $planta->predio_id . '/' . $planta->id }}" style="width: 100%; height: auto; display: block;" alt="Planta Baixa">

    <!-- Renderização dos Marcadores Salvos -->
    @include('plantas.partials.markers')

    <!-- Formulário Pop-up Nativo (Aparece no local do clique) -->
    <div id="popoverForm" style="display: none; position: absolute; background: #fff; border: 1px solid #000; padding: 12px; z-index: 1000; min-width: 250px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

        <!-- Título Dinâmico -->
        <strong id="formTitle" style="display: block; font-size: 13px; margin-bottom: 8px;">Selecione o Ponto:</strong>

        <!-- Formulário 1: Salvar/Atualizar Coordenada -->
        <form action="/plantas/{{ $planta->id }}" method="POST" id="mainForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="x" id="inputX">
            <input type="hidden" name="y" id="inputY">
            <input type="hidden" name="planta_id" value="{{ $planta->id }}">

            <select name="patch_panel_sala_id" id="patch_panel_sala_id" required style="width: 100%; padding: 5px; margin-bottom: 10px;">
                <option value="">-- Selecione --</option>
                @foreach($pontosSemMarcacao as $ponto)
                    <option value="{{ $ponto->id }}">
                        {{ optional(optional($ponto->patchPanel)->rack)->nome }}{{ $ponto->porta }} 
                        @if($ponto->sala) ({{ $ponto->sala->nome }}) @endif
                    </option>
                @endforeach
            </select>

            <div style="display: flex; justify-content: space-between; gap: 5px;">
                <button type="submit" id="btnSalvar" style="cursor: pointer;">Salvar Ponto</button>
                <button type="button" onclick="closeForm()" style="cursor: pointer;">Cancelar</button>
            </div>
        </form>

        <!-- Formulário 2: Remover Marcação Existente -->
        <form id="deleteForm" action="" method="POST" style="display: none; margin-top: 8px; border-top: 1px solid #eee; padding-top: 8px;">
            @csrf
            @method('DELETE')
            
            <button type="submit" onclick="return confirm('Desvincular esta marcação da planta?');" style="width: 100%; background: #dc3545; color: #fff; border: none; padding: 6px; cursor: pointer; border-radius: 3px; font-weight: bold;">
                Remover Marcação
            </button>
        </form>

    </div>

</div>

<script>
    const svgContainer = document.getElementById('svg-container');
    const svgImage = document.getElementById('svg-image');
    const popoverForm = document.getElementById('popoverForm');
    const deleteForm = document.getElementById('deleteForm');
    const mainForm = document.getElementById('mainForm');

    svgContainer.addEventListener('click', function (e) {
        const marker = e.target.closest('.marker-item');
        const rect = svgImage.getBoundingClientRect();

        let clickX = e.clientX - rect.left;
        let clickY = e.clientY - rect.top;

        if (marker) {
            // === MODO: REMOÇÃO ===
            e.stopPropagation();
            const markerId = marker.dataset.id;
            const markerNome = marker.dataset.nome;

            document.getElementById('formTitle').innerText = 'Ponto: ' + markerNome;
            
            // Define a rota exata com o ID do PatchPanelSala
            deleteForm.action = `/plantas/${markerId}/unmark`;

            mainForm.style.display = 'none';
            deleteForm.style.display = 'block';

        } else if (e.target === svgImage) {
            // === MODO: NOVA MARCAÇÃO ===
            const xPercent = (clickX / rect.width) * 100;
            const yPercent = (clickY / rect.height) * 100;

            document.getElementById('formTitle').innerText = 'Selecione o Ponto:';
            document.getElementById('inputX').value = xPercent.toFixed(2);
            document.getElementById('inputY').value = yPercent.toFixed(2);

            mainForm.style.display = 'block';
            deleteForm.style.display = 'none';
        } else {
            return;
        }

        // Posicionamento do Popover
        const formWidth = 260;
        let leftPos = clickX;
        if (clickX + formWidth > rect.width) {
            leftPos = clickX - formWidth;
        }

        popoverForm.style.left = leftPos + 'px';
        popoverForm.style.top = clickY + 'px';
        popoverForm.style.display = 'block';
    });

    function closeForm() {
        popoverForm.style.display = 'none';
        const selectPonto = document.getElementById('patch_panel_sala_id');
        if (selectPonto) selectPonto.value = '';
    }
</script>