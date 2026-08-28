@extends('main')

@section('content')
<!-- Inclusão do Panzoom via CDN -->
<script src="https://unpkg.com/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>

<div class="container-fluid py-3">
    <!-- Controles Superiores -->
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
    <div id="viewport" style="position: relative; width: 100%; height: 75vh; overflow: hidden; border: 1px solid #ccc; background-color: #f8fafc; border-radius: 6px;" class="mb-4">

        <!-- Target do Panzoom -->
        <div id="panzoom-target" style="position: relative; width: 100%; transform-origin: 0 0; cursor: grab;">

            <!-- Imagem SVG da Planta Baixa -->
            <img id="svg-image" src="{{ '/plantas/' . $planta->predio_id . '/' . $planta->id }}" style="width: 100%; height: auto; display: block; user-select: none;" draggable="false" alt="{{ $planta->name }}">

            <!-- Renderização dos Marcadores Visíveis -->
            @include('plantas.partials.markers', ['markers' => $markers])

        </div>
    </div>

    <!-- Tabela Detalhada de Pontos Visíveis Agrupados por Sala -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Detalhamento dos Pontos por Sala</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ponto</th>
                            <th>Sala</th>
                            <th>Tipo</th>
                            <th>Comprimento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($markers->groupBy(fn($item) => optional($item->sala)->nome ?? 'Sem Sala Definida') as $nomeSala => $pontosDaSala)
                            <!-- Cabeçalho da Sala -->
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-bold text-uppercase py-2 text-center">
                                    <i class="bi bi-door-closed me-1"></i> {{ $nomeSala }}
                                </td>
                            </tr>

                            <!-- Pontos Pertencentes à Sala -->
                            @foreach($pontosDaSala as $ponto)
                                @php
                                    $nomePonto = optional(optional($ponto->patchPanel)->rack)->nome . '-' . optional($ponto->patchPanel)->nome . '-' . $ponto->porta;
                                    $corTipo = optional($ponto->tipoPorta)->cor ?? '#ef4444';
                                @endphp
                                <tr>
                                    <td class="fw-bold ps-4">{{ $nomePonto }}</td>
                                    <td>{{ optional($ponto->sala)->nome ?? '-' }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $corTipo }}; font-weight: 500;">
                                            {{ optional($ponto->tipoPorta)->nome ?? 'Não definido' }}
                                        </span>
                                    </td>
                                    <td>{{ $ponto->tamanho ? number_format($ponto->tamanho, 2, ',', '.') . ' m' : '-' }}</td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    Nenhum ponto visível nesta planta.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <!-- Rodapé da Tabela com a Soma dos Comprimentos -->
                    <tfoot class="table-dark">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Comprimento Total de Cabeamento:</td>
                            <td class="fw-bold fs-6">
                                {{ number_format($markers->sum('tamanho'), 2, ',', '.') }} m
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const viewport = document.getElementById('viewport');
    const panzoomTarget = document.getElementById('panzoom-target');
    const svgImage = document.getElementById('svg-image');

    let panzoom;

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
    }

    // Inicialização do Panzoom ao carregar a imagem SVG
    if (svgImage.complete) {
        initPanzoom();
    } else {
        svgImage.addEventListener('load', initPanzoom);
    }

    // Previne zoom por atalhos de teclado (Ctrl +, Ctrl -, Ctrl 0, Cmd +, etc.)
    window.addEventListener('keydown', function (e) {
        if (
            (e.ctrlKey || e.metaKey) &&
            (
                e.key === '+' ||
                e.key === '-' ||
                e.key === '=' ||
                e.key === '0' ||
                e.code === 'NumpadAdd' ||
                e.code === 'NumpadSubtract'
            )
        ) {
            e.preventDefault();
        }
    }, { passive: false });

    // Previne zoom através da roda do mouse segurando Ctrl (Ctrl + Scroll)
    window.addEventListener('wheel', function (e) {
        if (e.ctrlKey) {
            e.preventDefault();
        }
    }, { passive: false });
</script>
@endsection