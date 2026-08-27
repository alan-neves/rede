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

    <!-- Formulário Pop-up Nativo (Edição / Inserção) -->
    <div id="popoverForm" style="display: none; position: absolute; background: #fff; border: 1px solid #000; padding: 12px; z-index: 1000; min-width: 280px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 6px;">

        <strong id="formTitle" style="display: block; font-size: 13px; margin-bottom: 8px;">Selecione o Ponto:</strong>

        <!-- Formulário Unificado: Salvar/Atualizar -->
        <form action="/plantas/{{ $planta->id }}" method="POST" id="mainForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="PUT">

            <input type="hidden" name="x" id="inputX">
            <input type="hidden" name="y" id="inputY">
            <input type="hidden" name="planta_id" value="{{ $planta->id }}">

            <!-- Campo Ponto para Modo Inserção -->
            <div id="divSelectPonto" style="margin-bottom: 8px;">
                <label style="display: block; font-size: 11px; margin-bottom: 2px; font-weight: bold;">Ponto:</label>
                <select name="patch_panel_sala_id" id="patch_panel_sala_id" style="width: 100%; padding: 5px; font-size: 12px;">
                    <option value="">-- Selecione --</option>
                    @foreach($pontosSemMarcacao as $ponto)
                        <option value="{{ $ponto->id }}"
                                data-tipo="{{ $ponto->tipo_porta_id }}"
                                data-comentario="{{ $ponto->comentario }}"
                                data-tamanho="{{ $ponto->tamanho }}"
                                data-fontsize="{{ $ponto->fontsize ?? 12 }}">
                            {{ optional(optional($ponto->patchPanel)->rack)->nome }}-{{ optional($ponto->patchPanel)->nome }}-{{ $ponto->porta }} 
                            @if($ponto->sala) ({{ $ponto->sala->nome }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Campo de Leitura Apenas no Modo Edição -->
            <div id="divReadonlyPonto" style="display: none; margin-bottom: 8px;">
                <label style="display: block; font-size: 11px; margin-bottom: 2px; font-weight: bold;">Ponto Selecionado:</label>
                <input type="text" id="inputPontoNome" readonly style="width: 100%; padding: 5px; font-size: 12px; background: #e9ecef; border: 1px solid #ced4da; border-radius: 3px; box-sizing: border-box;">
            </div>

            <!-- Tipo de Porta -->
            <div style="margin-bottom: 8px;">
                <label for="tipo_porta_id" style="display: block; font-size: 11px; margin-bottom: 2px;">Tipo de Porta (Opcional):</label>
                <select name="tipo_porta_id" id="tipo_porta_id" style="width: 100%; padding: 5px; font-size: 12px;">
                    <option value="">-- Não informar tipo --</option>
                    @if(isset($tipoPortas))
                        @foreach($tipoPortas as $tipoPorta)
                            <option value="{{ $tipoPorta->id }}">{{ $tipoPorta->nome }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Comentário -->
            <div style="margin-bottom: 8px;">
                <label for="comentario" style="display: block; font-size: 11px; margin-bottom: 2px;">Comentário (Opcional):</label>
                <input type="text" name="comentario" id="comentario" placeholder="Ex: Atrás da mesa" style="width: 100%; padding: 5px; box-sizing: border-box; font-size: 12px;">
            </div>

            <!-- Comprimento Cabeamento Horizontal (tamanho) -->
            <div style="margin-bottom: 8px;">
                <label for="tamanho" style="display: block; font-size: 11px; margin-bottom: 2px;">Comprimento Cabeamento Horizontal (m):</label>
                <div style="display: flex; align-items: center;">
                    <input type="number" step="0.01" name="tamanho" id="tamanho" placeholder="0.00" style="width: 100%; padding: 5px; box-sizing: border-box; font-size: 12px; border-top-right-radius: 0; border-bottom-right-radius: 0;">
                    <span style="background: #e9ecef; border: 1px solid #767676; border-left: none; padding: 4px 8px; font-size: 12px; border-top-right-radius: 3px; border-bottom-right-radius: 3px;">m</span>
                </div>
            </div>

            <!-- Tamanho da Fonte -->
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 11px; margin-bottom: 2px;">Tamanho da Fonte (px):</label>
                <input type="number" name="fontsize" id="inputFontsize" value="12" min="6" max="40" required style="width: 100%; padding: 5px; box-sizing: border-box; font-size: 12px;">
            </div>

            <!-- Ações do Formulário principal -->
            <div style="display: flex; justify-content: space-between; gap: 5px;">
                <button type="submit" id="btnSalvar" style="cursor: pointer; padding: 5px 10px; font-size: 12px; background: #0d6efd; color: #fff; border: none; border-radius: 3px;">Salvar Ponto</button>
                <button type="button" onclick="closeForm()" style="cursor: pointer; padding: 5px 10px; font-size: 12px;">Cancelar</button>
            </div>
        </form>

        <!-- Formulário Separado: Apenas para Ação de Remoção -->
        <form id="deleteForm" action="" method="POST" style="display: none; margin-top: 8px; border-top: 1px solid #eee; padding-top: 8px;">
            @csrf
            @method('DELETE')
            
            <button type="submit" onclick="return confirm('Desvincular esta marcação da planta?');" style="width: 100%; background: #dc3545; color: #fff; border: none; padding: 6px; cursor: pointer; border-radius: 3px; font-weight: bold; font-size: 12px;">
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
    const selectPonto = document.getElementById('patch_panel_sala_id');
    const divSelectPonto = document.getElementById('divSelectPonto');
    const divReadonlyPonto = document.getElementById('divReadonlyPonto');

    let panzoom;
    let justDragged = false;

    function initPanzoom() {
        panzoom = Panzoom(panzoomTarget, {
            maxScale: 6,
            minScale: 0.8,
            contain: 'outside'
        });

        setTimeout(() => { panzoom.reset(); }, 50);

        viewport.addEventListener('wheel', panzoom.zoomWithWheel);

        document.getElementById('btnZoomIn').addEventListener('click', panzoom.zoomIn);
        document.getElementById('btnZoomOut').addEventListener('click', panzoom.zoomOut);
        document.getElementById('btnZoomReset').addEventListener('click', panzoom.reset);

        // Atrela eventos de arrasto (drag & drop) e tooltip hover nos marcadores
        makeMarkersDraggable();

        let startX = 0;
        let startY = 0;

        panzoomTarget.addEventListener('pointerdown', function(e) {
            startX = e.clientX;
            startY = e.clientY;
        });

        panzoomTarget.addEventListener('panzoomend', function(e) {
            if (justDragged) {
                setTimeout(() => { justDragged = false; }, 100);
                return;
            }

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
                // MODO EDITAR / EXCLUIR
                abrirFormEdicao(marker, originalEvent);
            } else if (targetElement === svgImage) {
                // MODO CRIAR
                abrirFormCriacao(xPercent, yPercent, originalEvent);
            }
        });
    }

    // MODO EDIÇÃO
    function abrirFormEdicao(marker, event) {
        const markerId = marker.dataset.id;
        const markerNome = marker.dataset.nome || ('Ponto #' + markerId);

        document.getElementById('formTitle').innerText = 'Editar Ponto: ' + markerNome;

        mainForm.action = "/plantas/{{ $planta->id }}";
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('btnSalvar').innerText = 'Atualizar Ponto';

        // Garante ou cria o input hidden com o ID do ponto
        let inputHiddenPonto = document.getElementById('hidden_patch_panel_sala_id');
        if (!inputHiddenPonto) {
            inputHiddenPonto = document.createElement('input');
            inputHiddenPonto.type = 'hidden';
            inputHiddenPonto.id = 'hidden_patch_panel_sala_id';
            inputHiddenPonto.name = 'patch_panel_sala_id';
            mainForm.appendChild(inputHiddenPonto);
        }
        inputHiddenPonto.value = markerId;

        // Coordenadas
        document.getElementById('inputX').value = parseFloat(marker.style.left) || 0;
        document.getElementById('inputY').value = parseFloat(marker.style.top) || 0;

        divSelectPonto.style.display = 'none';
        selectPonto.required = false;

        divReadonlyPonto.style.display = 'block';
        document.getElementById('inputPontoNome').value = markerNome;

        // Preenche os campos do formulário com os datasets do elemento
        if (document.getElementById('tipo_porta_id')) {
            document.getElementById('tipo_porta_id').value = marker.dataset.tipo || '';
        }
        if (document.getElementById('comentario')) {
            document.getElementById('comentario').value = marker.dataset.comentario || '';
        }
        if (document.getElementById('tamanho')) {
            document.getElementById('tamanho').value = marker.dataset.tamanho || '';
        }
        if (document.getElementById('inputFontsize')) {
            document.getElementById('inputFontsize').value = marker.dataset.fontsize || '12';
        }

        // Configura rota de remoção
        deleteForm.action = `/plantas/${markerId}/unmark`;
        deleteForm.style.display = 'block';

        posicionarPopover(event);
    }

    // MODO CRIAÇÃO
    function abrirFormCriacao(x, y, event) {
        closeForm();

        const inputHiddenPonto = document.getElementById('hidden_patch_panel_sala_id');
        if (inputHiddenPonto) {
            inputHiddenPonto.remove();
        }

        document.getElementById('formTitle').innerText = 'Selecione o Ponto:';
        mainForm.action = "/plantas/{{ $planta->id }}";
        document.getElementById('formMethod').value = 'PUT';

        document.getElementById('inputX').value = x.toFixed(2);
        document.getElementById('inputY').value = y.toFixed(2);
        document.getElementById('btnSalvar').innerText = 'Salvar Ponto';

        divSelectPonto.style.display = 'block';
        selectPonto.required = true;

        divReadonlyPonto.style.display = 'none';
        deleteForm.style.display = 'none';

        posicionarPopover(event);
    }

    function posicionarPopover(event) {
        const viewportRect = viewport.getBoundingClientRect();
        let popoverX = event.clientX - viewportRect.left;
        let popoverY = event.clientY - viewportRect.top;

        if (popoverX + 280 > viewportRect.width) {
            popoverX -= 280;
        }

        popoverForm.style.left = popoverX + 'px';
        popoverForm.style.top = popoverY + 'px';
        popoverForm.style.display = 'block';
    }

    function makeMarkersDraggable() {
        const markers = panzoomTarget.querySelectorAll('.marker-ponto, .marker-item');

        markers.forEach(marker => {
            marker.style.cursor = 'grab';

            // Adiciona o atributo 'title' nativo para exibir o comentário ao passar o mouse
            const nome = marker.dataset.nome || '';
            const comentario = marker.dataset.comentario || '';
            let tooltipText = nome;
            if (comentario) {
                tooltipText += `\nObs: ${comentario}`;
            }
            marker.setAttribute('title', tooltipText);

            marker.addEventListener('pointerdown', function(e) {
                e.stopPropagation(); // Impede que o panzoom arraste o mapa

                const currentMarker = this;
                let isMove = false;
                const startX = e.clientX;
                const startY = e.clientY;

                panzoom.setOptions({ disablePan: true });
                currentMarker.style.cursor = 'grabbing';

                const onPointerMove = (moveEvent) => {
                    const deltaX = Math.abs(moveEvent.clientX - startX);
                    const deltaY = Math.abs(moveEvent.clientY - startY);

                    if (deltaX > 3 || deltaY > 3) {
                        isMove = true;
                        justDragged = true;
                        closeForm();

                        const rect = svgImage.getBoundingClientRect();

                        let newX = ((moveEvent.clientX - rect.left) / rect.width) * 100;
                        let newY = ((moveEvent.clientY - rect.top) / rect.height) * 100;

                        newX = Math.max(0, Math.min(100, newX));
                        newY = Math.max(0, Math.min(100, newY));

                        currentMarker.style.left = newX.toFixed(2) + '%';
                        currentMarker.style.top = newY.toFixed(2) + '%';
                    }
                };

                const onPointerUp = (upEvent) => {
                    document.removeEventListener('pointermove', onPointerMove);
                    document.removeEventListener('pointerup', onPointerUp);

                    panzoom.setOptions({ disablePan: false });
                    currentMarker.style.cursor = 'grab';

                    if (isMove) {
                        const finalX = parseFloat(currentMarker.style.left);
                        const finalY = parseFloat(currentMarker.style.top);
                        const markerId = currentMarker.dataset.id;

                        salvarNovaPosicao(markerId, finalX, finalY);
                    } else {
                        abrirFormEdicao(currentMarker, upEvent);
                    }
                };

                document.addEventListener('pointermove', onPointerMove);
                document.addEventListener('pointerup', onPointerUp);
            });
        });
    }

    function salvarNovaPosicao(id, x, y) {
        const token = document.querySelector('input[name="_token"]')?.value;

        fetch(`/plantas/{{ $planta->id }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                _method: 'PUT',
                x: x.toFixed(2),
                y: y.toFixed(2),
                planta_id: '{{ $planta->id }}',
                patch_panel_sala_id: id
            })
        })
        .then(async response => {
            if (!response.ok) {
                const errData = await response.json().catch(() => ({}));
                console.error('Erro ao salvar no banco:', response.status, errData);
                throw new Error(`Erro ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Posição atualizada com sucesso:', data);
        })
        .catch(error => {
            console.error('Erro ao mover o ponto:', error);
            alert('Não foi possível salvar a nova posição no banco de dados. Veja o console (F12).');
        });
    }

    if (selectPonto) {
        selectPonto.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];

            if (this.value) {
                const tipo = selectedOption.getAttribute('data-tipo') || '';
                const comentario = selectedOption.getAttribute('data-comentario') || '';
                const tamanho = selectedOption.getAttribute('data-tamanho') || '';
                const fontsize = selectedOption.getAttribute('data-fontsize') || '12';

                if (document.getElementById('tipo_porta_id')) document.getElementById('tipo_porta_id').value = tipo;
                if (document.getElementById('comentario')) document.getElementById('comentario').value = comentario;
                if (document.getElementById('tamanho')) document.getElementById('tamanho').value = tamanho;
                if (document.getElementById('inputFontsize')) document.getElementById('inputFontsize').value = fontsize;
            } else {
                limparCamposFormulario();
            }
        });
    }

    // Inicialização
    if (svgImage.complete) {
        initPanzoom();
    } else {
        svgImage.addEventListener('load', initPanzoom);
    }

    function limparCamposFormulario() {
        const selectTipo = document.getElementById('tipo_porta_id');
        const inputComentario = document.getElementById('comentario');
        const inputTamanho = document.getElementById('tamanho');
        const inputFontsize = document.getElementById('inputFontsize');

        if (selectTipo) selectTipo.value = '';
        if (inputComentario) inputComentario.value = '';
        if (inputTamanho) inputTamanho.value = '';
        if (inputFontsize) inputFontsize.value = '12';
    }

    function closeForm() {
        popoverForm.style.display = 'none';
        if (selectPonto) {
            selectPonto.value = '';
        }
        limparCamposFormulario();
    }
</script>