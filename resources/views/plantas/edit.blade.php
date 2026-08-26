<!-- Inclusão do Panzoom via CDN -->
<script src="https://unpkg.com/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
    <a href="/plantas/{{ $planta->predio_id }}" style="text-decoration: none; color: #000; background-color: #f0f0f0; padding: 6px 12px; border-radius: 4px; font-size: 14px;">
        &larr; Voltar
    </a>

    <!-- Barra de Controles de Zoom -->
    <div style="display: flex; gap: 5px; align-items: center; background: #f8fafc; padding: 4px 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
        <button type="button" id="btnZoomIn" style="padding: 4px 10px; font-weight: bold; cursor: pointer;">+</button>
        <button type="button" id="btnZoomOut" style="padding: 4px 10px; font-weight: bold; cursor: pointer;">-</button>
        <button type="button" id="btnZoomReset" style="padding: 4px 10px; cursor: pointer; font-size: 12px;">Redefinir Zoom</button>
        <span style="font-size: 11px; color: #64748b; margin-left: 5px;">(Use o scroll do mouse ou clique e arraste para mover)</span>
    </div>
</div>

<!-- Viewport fixa para conter o zoom -->
<div id="viewport" style="position: relative; width: 100%; height: 80vh; overflow: hidden; border: 1px solid #ccc; background-color: #f8fafc; border-radius: 6px;">

    <!-- Target do Panzoom -->
    <div id="panzoom-target" style="position: relative; width: 100%; transform-origin: 0 0; cursor: grab;">

        <!-- Imagem SVG da Planta Baixa -->
        <img id="svg-image" src="{{ '/plantas/' . $planta->predio_id . '/' . $planta->id }}" style="width: 100%; height: auto; display: block; user-select: none;" draggable="false" alt="Planta Baixa">

        <!-- Renderização dos Marcadores Salvos -->
        @include('plantas.partials.markers')
        <div style="pointer-events: none;">
            @include('salas.partials.markers', ['salas' => $salasMarkerd])
        </div>

    </div>

    <!-- Formulário Pop-up Nativo -->
    <div id="popoverForm" style="display: none; position: absolute; background: #fff; border: 1px solid #000; padding: 12px; z-index: 1000; min-width: 260px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 4px;">

        <strong id="formTitle" style="display: block; font-size: 13px; margin-bottom: 8px;">Selecione o Ponto:</strong>

        <!-- Formulário 1: Salvar Coordenada -->
        <form action="/plantas/{{ $planta->id }}" method="POST" id="mainForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="x" id="inputX">
            <input type="hidden" name="y" id="inputY">
            <input type="hidden" name="planta_id" value="{{ $planta->id }}">

            <div style="margin-bottom: 8px;">
                <label style="display: block; font-size: 11px; margin-bottom: 2px;">Ponto Disponível:</label>
                <select name="patch_panel_sala_id" id="patch_panel_sala_id" required style="width: 100%; padding: 5px;" onchange="atualizarComentarioDoSelect(this)">
                    <option value="" data-comentario="">-- Selecione um ponto --</option>
                    @foreach($pontosSemMarcacao as $ponto)
                        {{-- Filtra no blade garantindo que pontos marcados não apareçam no select --}}
                        @if(empty($ponto->x) && empty($ponto->y))
                            <option value="{{ $ponto->id }}" data-comentario="{{ $ponto->comentario }}">
                                {{ optional(optional($ponto->patchPanel)->rack)->nome }}-{{ $ponto->patchPanel->nome }}-{{ $ponto->porta }} 
                                @if($ponto->sala) ({{ $ponto->sala->nome }}) @endif
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- CAMPO: Fontsize -->
            <div style="margin-bottom: 8px;">
                <label style="display: block; font-size: 11px; margin-bottom: 2px;">Tamanho da Fonte (px):</label>
                <input type="number" name="fontsize" id="inputFontsize" value="12" min="2" max="40" required style="width: 100%; padding: 5px; box-sizing: border-box;">
            </div>

            <!-- CAMPO: Comentário -->
            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 11px; margin-bottom: 2px;">Comentário:</label>
                <textarea name="comentario" id="inputComentario" rows="2" placeholder="Digite um comentário..." style="width: 100%; padding: 5px; box-sizing: border-box; resize: vertical; font-family: sans-serif; font-size: 12px;"></textarea>
            </div>

            <div style="display: flex; justify-content: space-between; gap: 5px;">
                <button type="submit" id="btnSalvar" style="cursor: pointer;">Salvar Ponto</button>
                <button type="button" onclick="closeForm()" style="cursor: pointer;">Cancelar</button>
            </div>
        </form>

        <!-- Formulário 2: Remover Marcação Existente -->
        <form id="deleteForm" action="" method="POST" style="display: none; margin-top: 8px; border-top: 1px solid #eee; padding-top: 8px;">
            @csrf
            @method('DELETE')
            
            <p style="font-size: 11px; color: #64748b; margin-bottom: 8px;">Este ponto já está demarcado na planta.</p>

            <button type="submit" onclick="return confirm('Desvincular esta marcação da planta?');" style="width: 100%; background: #dc3545; color: #fff; border: none; padding: 6px; cursor: pointer; border-radius: 3px; font-weight: bold;">
                Remover Marcação
            </button>
        </form>

    </div>

</div>

<script>
    const viewport = document.getElementById('viewport');
    const panzoomTarget = document.getElementById('panzoom-target');
    const svgImage = document.getElementById('svg-image');
    const popoverForm = document.getElementById('popoverForm');
    const deleteForm = document.getElementById('deleteForm');
    const mainForm = document.getElementById('mainForm');
    const inputComentario = document.getElementById('inputComentario');
    const selectPonto = document.getElementById('patch_panel_sala_id');

    let panzoom;

    function initPanzoom() {
        panzoom = Panzoom(panzoomTarget, {
            maxScale: 6,
            minScale: 0.8,
            contain: 'outside'
        });

        setTimeout(() => {
            panzoom.reset();
        }, 50);

        viewport.addEventListener('wheel', panzoom.zoomWithWheel);

        document.getElementById('btnZoomIn').addEventListener('click', panzoom.zoomIn);
        document.getElementById('btnZoomOut').addEventListener('click', panzoom.zoomOut);
        document.getElementById('btnZoomReset').addEventListener('click', panzoom.reset);

        let startX = 0;
        let startY = 0;

        panzoomTarget.addEventListener('pointerdown', function(e) {
            startX = e.clientX;
            startY = e.clientY;
        });

        panzoomTarget.addEventListener('panzoomend', function(e) {
            const dist = Math.hypot(e.detail.originalEvent.clientX - startX, e.detail.originalEvent.clientY - startY);
            if (dist > 5) return;

            const originalEvent = e.detail.originalEvent;
            const targetElement = document.elementFromPoint(originalEvent.clientX, originalEvent.clientY);

            if (!targetElement) return;

            const marker = targetElement.closest('.marker-ponto, .marker-item');

            const rect = svgImage.getBoundingClientRect();
            const clickX = originalEvent.clientX - rect.left;
            const clickY = originalEvent.clientY - rect.top;

            const xPercent = (clickX / rect.width) * 100;
            const yPercent = (clickY / rect.height) * 100;

            if (marker && panzoomTarget.contains(marker)) {
                // === MODO: MARCADOR JÁ EXISTENTE ===
                // Permite apenas REMOVER a marcação para desvincular o ponto
                const markerId = marker.dataset.id;
                const markerNome = marker.dataset.nome;

                document.getElementById('formTitle').innerText = 'Ponto: ' + markerNome;
                deleteForm.action = `/plantas/${markerId}/unmark`;

                mainForm.style.display = 'none';
                deleteForm.style.display = 'block';

            } else if (targetElement === svgImage) {
                // === MODO: NOVA MARCAÇÃO ===
                // Verifica se existem pontos disponíveis para marcar
                if (selectPonto.options.length <= 1) {
                    alert('Todos os pontos já foram marcados nesta planta ou não há pontos disponíveis.');
                    return;
                }

                document.getElementById('formTitle').innerText = 'Selecione o Ponto:';
                document.getElementById('inputX').value = xPercent.toFixed(2);
                document.getElementById('inputY').value = yPercent.toFixed(2);
                
                selectPonto.value = '';
                inputComentario.value = '';

                mainForm.style.display = 'block';
                deleteForm.style.display = 'none';
            } else {
                return;
            }

            const viewportRect = viewport.getBoundingClientRect();
            let popoverX = originalEvent.clientX - viewportRect.left;
            let popoverY = originalEvent.clientY - viewportRect.top;

            if (popoverX + 260 > viewportRect.width) {
                popoverX -= 260;
            }

            popoverForm.style.left = popoverX + 'px';
            popoverForm.style.top = popoverY + 'px';
            popoverForm.style.display = 'block';
        });
    }

    function atualizarComentarioDoSelect(selectElement) {
        const optionSelecionada = selectElement.options[selectElement.selectedIndex];
        const comentario = optionSelecionada.getAttribute('data-comentario') || '';
        inputComentario.value = comentario;
    }

    if (svgImage.complete) {
        initPanzoom();
    } else {
        svgImage.addEventListener('load', initPanzoom);
    }

    function closeForm() {
        popoverForm.style.display = 'none';
        if (selectPonto) selectPonto.value = '';
        if (inputComentario) inputComentario.value = '';
    }
</script>