<!-- CDN da biblioteca Panzoom -->
<script src="https://unpkg.com/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>

<!-- Token CSRF do Laravel para requisições AJAX do Drag and Drop -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
    <a href="/plantas/{{ $planta->predio_id }}" style="text-decoration: none; color: #000; background-color: #f0f0f0; padding: 6px 12px; border-radius: 4px; font-size: 14px;">
        &larr; Voltar
    </a>

    <!-- Controles de Zoom -->
    <div style="display: flex; gap: 5px; align-items: center; background: #f8fafc; padding: 4px 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
        <button type="button" id="btnZoomIn" style="padding: 4px 10px; font-weight: bold; cursor: pointer;">+</button>
        <button type="button" id="btnZoomOut" style="padding: 4px 10px; font-weight: bold; cursor: pointer;">-</button>
        <button type="button" id="btnZoomReset" style="padding: 4px 10px; cursor: pointer; font-size: 12px;">Redefinir Zoom</button>
        <span style="font-size: 11px; color: #64748b; margin-left: 5px;">(Use o scroll do mouse para zoom ou clique e arraste para mover)</span>
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
    <div id="popoverForm" style="display: none; position: absolute; background: #fff; border: 1px solid #000; padding: 12px; z-index: 1000; min-width: 260px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

        <strong id="formTitle" style="display: block; font-size: 13px; margin-bottom: 8px;">Selecione a Sala:</strong>

        <!-- Formulário 1: Salvar/Editar Coordenada, Fonte e Descrição da Sala -->
        <form action="" method="POST" id="mainForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="x" id="inputX">
            <input type="hidden" name="y" id="inputY">
            <input type="hidden" name="planta_id" value="{{ $planta->id }}">

            <!-- Seleção da Sala (Oculto na Edição) -->
            <div id="groupSalaSelect" style="margin-bottom: 8px;">
                <label style="display: block; font-size: 11px; margin-bottom: 2px;">Sala:</label>
                <select name="sala_select" id="sala_select" style="width: 100%; padding: 5px;" onchange="updateFormAction()">
                    <option value="" data-descricao="">-- Selecione a Sala --</option>
                    @foreach($salasNotMarkerd as $sala)
                        <option value="{{ $sala->id }}" data-descricao="{{ $sala->descricao }}">{{ $sala->nome }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Descrição da Sala -->
            <div style="margin-bottom: 8px;">
                <label style="display: block; font-size: 11px; margin-bottom: 2px;">Descrição:</label>
                <input type="text" name="descricao" id="inputDescricao" placeholder="Ex: Laboratório de Informática" style="width: 100%; padding: 5px; box-sizing: border-box; font-size: 12px;">
            </div>

            <!-- Tamanho da Fonte -->
            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 11px; margin-bottom: 2px;">Tamanho da Fonte (px):</label>
                <input type="number" name="fontsize" id="inputFontsize" value="12" min="2" max="50" required style="width: 100%; padding: 5px; box-sizing: border-box;">
            </div>

            <div style="display: flex; justify-content: space-between; gap: 5px;">
                <button type="submit" id="btnSalvar" style="cursor: pointer;">Salvar Alterações</button>
                <button type="button" onclick="closeForm()" style="cursor: pointer;">Cancelar</button>
            </div>
        </form>

        <!-- Formulário 2: Remover Marcação (Rota DELETE) -->
        <form id="deleteForm" action="" method="POST" style="display: none; margin-top: 10px; border-top: 1px solid #ccc; padding-top: 8px;">
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
    const groupSalaSelect = document.getElementById('groupSalaSelect');
    const inputDescricao = document.getElementById('inputDescricao');
    const inputFontsize = document.getElementById('inputFontsize');
    const inputX = document.getElementById('inputX');
    const inputY = document.getElementById('inputY');
    const plantaId = "{{ $planta->id }}";

    let panzoom;
    let isDraggingMarker = false;

    function updateFormAction() {
        const selectedOption = salaSelect.options[salaSelect.selectedIndex];
        const salaId = salaSelect.value;

        if (salaId) {
            mainForm.action = `/salas/${salaId}/${plantaId}/mark`;
            inputDescricao.value = selectedOption.dataset.descricao || "";
        } else {
            mainForm.action = "";
            inputDescricao.value = "";
        }
    }

    mainForm.addEventListener('submit', function (e) {
        // Se estiver no modo de criação e nenhuma sala for selecionada
        if (groupSalaSelect.style.display !== 'none' && !salaSelect.value) {
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
            if (isDraggingMarker) return;

            const dist = Math.hypot(e.detail.originalEvent.clientX - startX, e.detail.originalEvent.clientY - startY);
            if (dist > 5) return;

            const originalEvent = e.detail.originalEvent;
            const targetElement = document.elementFromPoint(originalEvent.clientX, originalEvent.clientY);

            if (!targetElement) return;

            const rect = svgImage.getBoundingClientRect();
            const clickX = originalEvent.clientX - rect.left;
            const clickY = originalEvent.clientY - rect.top;

            const xPercent = (clickX / rect.width) * 100;
            const yPercent = (clickY / rect.height) * 100;

            if (targetElement === svgImage) {
                // === MODO: NOVA MARCAÇÃO DE SALA ===
                document.getElementById('formTitle').innerText = 'Selecione a Sala:';
                
                inputX.value = xPercent.toFixed(2);
                inputY.value = yPercent.toFixed(2);
                inputFontsize.value = 12;
                inputDescricao.value = "";
                salaSelect.value = "";
                salaSelect.required = true;

                groupSalaSelect.style.display = 'block';
                mainForm.action = "";
                mainForm.style.display = 'block';
                deleteForm.style.display = 'none';

                posicionarPopover(originalEvent);
            }
        });

        setupMarkerDrag();
    }

    function setupMarkerDrag() {
        const markers = document.querySelectorAll('.marker-item');

        markers.forEach(marker => {
            let activeDrag = false;

            marker.style.cursor = 'move';

            marker.addEventListener('pointerdown', function(e) {
                e.stopPropagation();
                activeDrag = false;

                const startClickX = e.clientX;
                const startClickY = e.clientY;

                const onPointerMove = (moveEvent) => {
                    const dist = Math.hypot(moveEvent.clientX - startClickX, moveEvent.clientY - startClickY);
                    
                    if (dist > 5) {
                        activeDrag = true;
                        isDraggingMarker = true;

                        const rect = svgImage.getBoundingClientRect();
                        let newX = ((moveEvent.clientX - rect.left) / rect.width) * 100;
                        let newY = ((moveEvent.clientY - rect.top) / rect.height) * 100;

                        newX = Math.max(0, Math.min(100, newX));
                        newY = Math.max(0, Math.min(100, newY));

                        marker.style.left = `${newX.toFixed(2)}%`;
                        marker.style.top = `${newY.toFixed(2)}%`;
                    }
                };

                const onPointerUp = (upEvent) => {
                    window.removeEventListener('pointermove', onPointerMove);
                    window.removeEventListener('pointerup', onPointerUp);

                    if (activeDrag) {
                        // === SE MOVEOU: Salva nova posição via AJAX ===
                        const rect = svgImage.getBoundingClientRect();
                        const finalX = (((upEvent.clientX - rect.left) / rect.width) * 100).toFixed(2);
                        const finalY = (((upEvent.clientY - rect.top) / rect.height) * 100).toFixed(2);

                        salvarNovaPosicaoSala(marker.dataset.id, finalX, finalY);

                        setTimeout(() => {
                            isDraggingMarker = false;
                        }, 100);
                    } else {
                        // === SE FOI APENAS UM CLIQUE: Edição (Fonte/Descrição) + Remoção ===
                        isDraggingMarker = false;

                        const salaId = marker.dataset.id;
                        const salaNome = marker.dataset.nome;
                        const salaFontsize = marker.dataset.fontsize || 12;
                        const salaDescricao = marker.dataset.descricao || "";

                        // Pega X e Y atuais do estilo do elemento para manter no formulário
                        const currentX = parseFloat(marker.style.left);
                        const currentY = parseFloat(marker.style.top);

                        document.getElementById('formTitle').innerText = 'Editar Sala: ' + salaNome;
                        
                        // Oculta o select pois a sala já está selecionada
                        groupSalaSelect.style.display = 'none';
                        salaSelect.required = false;

                        // Preenche os campos do formulário principal para atualização
                        mainForm.action = `/salas/${salaId}/${plantaId}/mark`;
                        inputX.value = currentX;
                        inputY.value = currentY;
                        inputFontsize.value = salaFontsize;
                        inputDescricao.value = salaDescricao;

                        // Configura o formulário de deleção
                        deleteForm.action = `/salas/${salaId}/unmark`;

                        mainForm.style.display = 'block';
                        deleteForm.style.display = 'block';

                        posicionarPopover(upEvent);
                    }
                };

                window.addEventListener('pointermove', onPointerMove);
                window.addEventListener('pointerup', onPointerUp);
            });
        });
    }

    function posicionarPopover(event) {
        const viewportRect = viewport.getBoundingClientRect();
        let popoverX = event.clientX - viewportRect.left;
        let popoverY = event.clientY - viewportRect.top;

        if (popoverX + 270 > viewportRect.width) {
            popoverX -= 270;
        }

        popoverForm.style.left = popoverX + 'px';
        popoverForm.style.top = popoverY + 'px';
        popoverForm.style.display = 'block';
    }

    function salvarNovaPosicaoSala(salaId, x, y) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(`/salas/${salaId}/${plantaId}/mark`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                x: x,
                y: y,
                planta_id: plantaId
            })
        })
        .then(response => {
            if (!response.ok) {
                alert('Erro ao atualizar a posição da sala.');
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar coordenada:', error);
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
        inputDescricao.value = '';
    }

    // Bloqueio de Zoom do Navegador (Teclado e Roda do Mouse)
    window.addEventListener('keydown', function (e) {
        // Verifica se Ctrl (Windows/Linux) ou Cmd (Mac) está pressionado
        if (e.ctrlKey || e.metaKey) {
            const key = e.key;
            const code = e.code;

            // Combinações de Zoom In (+ / =), Zoom Out (- / _) e Reset (0)
            if (
                key === '+' || key === '=' || 
                key === '-' || key === '_' || 
                key === '0' ||
                code === 'NumpadAdd' || 
                code === 'NumpadSubtract' ||
                code === 'Minus' || 
                code === 'Equal' || 
                code === 'Digit0'
            ) {
                e.preventDefault();
                e.stopPropagation();
            }
        }
    }, true); // O 'true' ativa o evento no modo Captura, garantindo prioridade total

    window.addEventListener('wheel', function (e) {
        if (e.ctrlKey || e.metaKey) {
            e.preventDefault();
        }
    }, { passive: false });
</script>