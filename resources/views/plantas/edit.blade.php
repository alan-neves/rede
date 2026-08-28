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
            <div class="mb-3">
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
@endsection