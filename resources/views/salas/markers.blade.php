<!-- CDN da biblioteca Panzoom -->
<script src="https://unpkg.com/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
    <a href="/plantas/{{ $planta->predio_id }}" style="text-decoration: none; color: #000; background-color: #f0f0f0; padding: 6px 12px; border-radius: 4px; font-size: 14px;">
        &larr; Voltar
    </a>

    <!-- Controles de Zoom -->
    <div style="display: flex; gap: 5px; align-items: center; background: #f8fafc; padding: 4px 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
        <button type="button" id="btnZoomIn" style="padding: 4px 10px; font-weight: bold; cursor: pointer;">+</button>
        <button type="button" id="btnZoomOut" style="padding: 4px 10px; font-weight: bold; cursor: pointer;">-</button>
        <button type="button" id="btnZoomReset" style="padding: 4px 10px; cursor: pointer; font-size: 12px;">Redefinir Zoom</button>
        <span style="font-size: 11px; color: #64748b; margin-left: 5px;">(Use o scroll do mouse ou clique e arraste para mover)</span>
    </div>
</div>

<!-- Viewport fixa para enquadrar a planta -->
<div id="viewport" style="position: relative; width: 100%; height: 80vh; overflow: hidden; border: 1px solid #ccc; background-color: #f8fafc; border-radius: 6px;">

    <!-- Alvo do Panzoom -->
    <div id="panzoom-target" style="position: relative; width: 100%; transform-origin: 0 0; cursor: grab;">

        <!-- Imagem da Planta Baixa -->
        <img id="svg-image" src="{{ '/plantas/' . $planta->predio_id . '/' . $planta->id }}" style="width: 100%; height: auto; display: block; user-select: none;" draggable="false" alt="Planta Baixa">

        <!-- Renderização dos Marcadores das Salas -->
        @include('salas.partials.markers', ['salas' => $salasMarkerd])

    </div>

    <!-- Formulário Pop-up Nativo -->
    <div id="popoverForm" style="display: none; position: absolute; background: #fff; border: 1px solid #000; padding: 12px; z-index: 1000; min-width: 250px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

        <strong id="formTitle" style="display: block; font-size: 13px; margin-bottom: 8px;">Selecione a Sala:</strong>

        <!-- Formulário 1: Salvar Coordenada da Sala -->
        <form action="" method="POST" id="mainForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="x" id="inputX">
            <input type="hidden" name="y" id="inputY">
            <input type="hidden" name="planta_id" value="{{ $planta->id }}">

            <div style="margin-bottom: 8px;">
                <label style="display: block; font-size: 11px; margin-bottom: 2px;">Sala:</label>
                <select name="sala_select" id="sala_select" required style="width: 100%; padding: 5px;" onchange="updateFormAction()">
                    <option value="">-- Selecione a Sala --</option>
                    @foreach($salasNotMarkerd as $sala)
                        <option value="{{ $sala->id }}">{{ $sala->nome }}</option>
                    @endforeach
                </select>
            </div>

            <!-- CAMPO ADICIONADO: Tamanho da Fonte -->
            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 11px; margin-bottom: 2px;">Tamanho da Fonte (px):</label>
                <input type="number" name="fontsize" id="inputFontsize" value="12" min="6" max="50" required style="width: 100%; padding: 5px; box-sizing: border-box;">
            </div>

            @if($salasNotMarkerd->isEmpty())
                <p style="color: red; font-size: 11px; margin-top: 0;">Nenhuma sala pendente de marcação neste prédio.</p>
            @endif

            <div style="display: flex; justify-content: space-between; gap: 5px;">
                <button type="submit" id="btnSalvar" @if($salasNotMarkerd->isEmpty()) disabled @endif style="cursor: pointer;">Salvar Sala</button>
                <button type="button" onclick="closeForm()" style="cursor: pointer;">Cancelar</button>
            </div>
        </form>

        <!-- Formulário 2: Remover Marcação (Rota DELETE) -->
        <form id="deleteForm" action="" method="POST" style="display: none; margin-top: 8px; border-top: 1px solid #eee; padding-top: 8px;">
            @csrf
            @method('DELETE')
            
            <button type="submit" onclick="return confirm('Desvincular esta sala da planta baixa?');" style="width: 100%; background: #dc3545; color: #fff; border: none; padding: 6px; cursor: pointer; border-radius: 3px; font-weight: bold;">
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
    const salaSelect = document.getElementById('sala_select');
    const plantaId = "{{ $planta->id }}";

    let panzoom;

    function updateFormAction() {
        const salaId = salaSelect.value;
        if (salaId) {
            mainForm.action = `/salas/${salaId}/${plantaId}/mark`;
        } else {
            mainForm.action = "";
        }
    }

    mainForm.addEventListener('submit', function (e) {
        if (!salaSelect.value) {
            e.preventDefault();
            alert('Por favor, selecione uma sala.');
        }
    });

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

            const marker = targetElement.closest('.marker-item');
            const rect = svgImage.getBoundingClientRect();
            const clickX = originalEvent.clientX - rect.left;
            const clickY = originalEvent.clientY - rect.top;

            const xPercent = (clickX / rect.width) * 100;
            const yPercent = (clickY / rect.height) * 100;

            if (marker && panzoomTarget.contains(marker)) {
                // === MODO: REMOÇÃO DA MARCAÇÃO DA SALA ===
                const salaId = marker.dataset.id;
                const salaNome = marker.dataset.nome;

                document.getElementById('formTitle').innerText = 'Sala: ' + salaNome;
                deleteForm.action = `/salas/${salaId}/unmark`;

                mainForm.style.display = 'none';
                deleteForm.style.display = 'block';

            } else if (targetElement === svgImage) {
                // === MODO: NOVA MARCAÇÃO DE SALA ===
                document.getElementById('formTitle').innerText = 'Selecione a Sala:';
                document.getElementById('inputX').value = xPercent.toFixed(2);
                document.getElementById('inputY').value = yPercent.toFixed(2);
                document.getElementById('inputFontsize').value = 12; // Reseta com o padrão 12px

                salaSelect.value = "";
                mainForm.action = "";
                mainForm.style.display = 'block';
                deleteForm.style.display = 'none';
            } else {
                return;
            }

            // Posiciona o Popover dentro da viewport
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

    if (svgImage.complete) {
        initPanzoom();
    } else {
        svgImage.addEventListener('load', initPanzoom);
    }

    function closeForm() {
        popoverForm.style.display = 'none';
        salaSelect.value = '';
    }
</script>