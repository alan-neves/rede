@extends('main')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">Editar Planta Baixa: {{ $planta->name }}</h5>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="/plantas/{{ $planta->id }}">
            @csrf
            @method('PUT')

            <input type="hidden" name="predio_id" value="{{ $planta->predio_id }}">

            <!-- Campo: Nome da Planta -->
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Nome da Planta *</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control" 
                       placeholder="Ex: Pavimento Térreo, Subsolo 1..." 
                       value="{{ old('name', $planta->name) }}" 
                       required>
            </div>

            <!-- Campo: Upload de Arquivo SVG (Opcional na edição) -->
            <div class="mb-3">
                <label for="planta" class="form-label fw-semibold">Substituir Arquivo SVG (Opcional)</label>
                <input type="file" 
                       id="planta" 
                       name="planta" 
                       accept=".svg" 
                       class="form-control">
                <div class="form-text">
                    Arquivo atual: <strong>{{ $planta->original_name ?? 'SVG anexado' }}</strong>.
                    Deixe em branco se desejar mantê-lo.
                </div>
            </div>

            <!-- Campo: Tornar Planta Pública -->
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" 
                           type="checkbox" 
                           role="switch" 
                           id="public" 
                           name="public" 
                           value="1" 
                           {{ old('public', $planta->public) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="public">
                        Deixar planta pública
                    </label>
                </div>
                <div class="form-text">Permite a visualização do mapa e marcadores sem exigência de login.</div>
            </div>

            <hr class="my-4">

            <!-- Seção: Pontos da Planta (Visibilidade) -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0">Visibilidade dos Pontos Cadastrados</h6>
                    @if(isset($pontos) && $pontos->count() > 0)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllPontos" onchange="toggleSelectAll(this)">
                            <label class="form-check-label fw-semibold" for="selectAllPontos">
                                Marcar / Desmarcar Todos
                            </label>
                        </div>
                    @endif
                </div>
                <div class="form-text mb-3">Selecione quais pontos desta planta estarão visíveis no mapa.</div>

                @if(isset($pontos) && $pontos->count() > 0)
                    <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                        <div class="row g-2">
                            @foreach($pontos as $ponto)
                                @php
                                    $nomePonto = optional(optional($ponto->patchPanel)->rack)->nome . '-' . optional($ponto->patchPanel)->nome . '-' . $ponto->porta;
                                    if ($ponto->sala) {
                                        $nomePonto .= ' (' . $ponto->sala->nome . ')';
                                    }
                                @endphp
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check bg-white p-2 border rounded">
                                        <input class="form-check-input ponto-checkbox" 
                                               type="checkbox" 
                                               name="pontos_visiveis[]" 
                                               value="{{ $ponto->id }}" 
                                               id="ponto_{{ $ponto->id }}"
                                               {{ old("pontos_visiveis.{$loop->index}", $ponto->visible) ? 'checked' : '' }}>
                                        <label class="form-check-label text-truncate d-block" for="ponto_{{ $ponto->id }}" title="{{ $nomePonto }}">
                                            {{ $nomePonto }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning mb-0" role="alert">
                        Nenhum ponto marcado para esta planta até o momento.
                    </div>
                @endif
            </div>

            <!-- Ações -->
            <div class="d-flex justify-content-between align-items-center">
                <a href="/plantas/{{ $planta->predio_id }}" class="btn btn-secondary">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-check-lg me-1"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleSelectAll(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.ponto-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = masterCheckbox.checked;
        });
    }
</script>
@endsection