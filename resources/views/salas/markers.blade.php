<a href="/plantas/{{ $planta->predio_id }}" style="display: inline-block; margin-bottom: 10px; text-decoration: none; color: #000; background-color: #f0f0f0; padding: 5px 10px; border-radius: 4px;">
    &larr; Voltar
</a>

<!-- Contêiner ocupando 100% da largura da página -->
<div id="svg-container" style="position: relative; width: 100%; display: block; cursor: crosshair;">

    <!-- Imagem da Planta Baixa -->
    <img id="svg-image" src="{{ '/plantas/' . $planta->predio_id . '/' . $planta->id }}" style="width: 100%; height: auto; display: block;" alt="Planta Baixa">

    <!-- Renderização dos Marcadores das Salas -->
    @foreach($salasMarkerd as $sala)
        <div class="marker-item" 
             data-id="{{ $sala->id }}" 
             data-nome="{{ $sala->nome }}"
             style="position: absolute; left: {{ $sala->x }}%; top: {{ $sala->y }}%; transform: translate(-50%, -100%); cursor: pointer; z-index: 10;">
            
            <!-- Ícone/Pin indicando a Sala -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="#2563eb" stroke="#1d4ed8" stroke-width="2" style="display: block; margin: 0 auto; pointer-events: none;">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
            
            <span style="background: #1e293b; color: #fff; font-size: 11px; padding: 2px 6px; white-space: nowrap; border-radius: 3px; font-weight: bold; pointer-events: none;">
                {{ $sala->nome }}
            </span>
        </div>
    @endforeach

    <!-- Formulário Pop-up Nativo -->
    <div id="popoverForm" style="display: none; position: absolute; background: #fff; border: 1px solid #000; padding: 12px; z-index: 1000; min-width: 250px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

        <!-- Título Dinâmico -->
        <strong id="formTitle" style="display: block; font-size: 13px; margin-bottom: 8px;">Selecione a Sala:</strong>

        <!-- Formulário 1: Salvar Coordenada da Sala -->
        <form action="" method="POST" id="mainForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="x" id="inputX">
            <input type="hidden" name="y" id="inputY">
            <input type="hidden" name="planta_id" value="{{ $planta->id }}">

            <select name="sala_select" id="sala_select" required style="width: 100%; padding: 5px; margin-bottom: 10px;" onchange="updateFormAction()">
                <option value="">-- Selecione a Sala --</option>
                @foreach($salasNotMarkerd as $sala)
                    <option value="{{ $sala->id }}">{{ $sala->nome }}</option>
                @endforeach
            </select>

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
    const svgContainer = document.getElementById('svg-container');
    const svgImage = document.getElementById('svg-image');
    const popoverForm = document.getElementById('popoverForm');
    const deleteForm = document.getElementById('deleteForm');
    const mainForm = document.getElementById('mainForm');
    const salaSelect = document.getElementById('sala_select');
    const plantaId = "{{ $planta->id }}";

    // Atualiza a action do formulário principal ao escolher a sala
    function updateFormAction() {
        const salaId = salaSelect.value;
        if (salaId) {
            mainForm.action = `/salas/${salaId}/${plantaId}/mark`;
        } else {
            mainForm.action = "";
        }
    }

    // Garante que o formulário não seja enviado sem selecionar uma sala
    mainForm.addEventListener('submit', function (e) {
        if (!salaSelect.value) {
            e.preventDefault();
            alert('Por favor, selecione uma sala.');
        }
    });

    svgContainer.addEventListener('click', function (e) {
        const marker = e.target.closest('.marker-item');
        const rect = svgImage.getBoundingClientRect();

        let clickX = e.clientX - rect.left;
        let clickY = e.clientY - rect.top;

        if (marker) {
            // === MODO: REMOÇÃO DA MARCAÇÃO DA SALA ===
            e.stopPropagation();
            const salaId = marker.dataset.id;
            const salaNome = marker.dataset.nome;

            document.getElementById('formTitle').innerText = 'Sala: ' + salaNome;
            
            // Aponta diretamente para a rota DELETE /salas/{sala}/unmark
            deleteForm.action = `/salas/${salaId}/unmark`;

            mainForm.style.display = 'none';
            deleteForm.style.display = 'block';

        } else if (e.target === svgImage) {
            // === MODO: NOVA MARCAÇÃO DE SALA ===
            const xPercent = (clickX / rect.width) * 100;
            const yPercent = (clickY / rect.height) * 100;

            document.getElementById('formTitle').innerText = 'Selecione a Sala:';
            document.getElementById('inputX').value = xPercent.toFixed(2);
            document.getElementById('inputY').value = yPercent.toFixed(2);

            salaSelect.value = "";
            mainForm.action = "";
            mainForm.style.display = 'block';
            deleteForm.style.display = 'none';
        } else {
            return;
        }

        // Posicionamento inteligente do Popover na tela
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
        salaSelect.value = '';
    }
</script>