@extends('main')

@section('content')
<div class="card">
    <div class="card-header bg-usp">
        <h1 class="h4 mb-0 text-dark">
            Editar Tipo de Porta - Porta {{ $porta }}
            <small class="text-muted d-block">Patch Panel: {{ $patchPanel->nome }} | Sala: {{ $sala->nome }}</small>
        </h1>
    </div>
    <div class="card-body">
        <form action="/patch-panels/{{ $patchPanel->id }}/atualizar-tipo-porta/{{ $sala->id }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="porta" value="{{ $porta }}">
            
            <div class="form-group">
                <label for="tipo_porta_id">Tipo de Porta:</label>
                <select class="form-control" name="tipo_porta_id" id="tipo_porta_id">
                    <option value="">-- Não informar tipo --</option>
                    @foreach($tipoPortas as $tipoPorta)
                        <option value="{{ $tipoPorta->id }}" {{ $tipoPortaAtual == $tipoPorta->id ? 'selected' : '' }}>
                            {{ $tipoPorta->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="comentario">Comentário (Opcional):</label>
                <input class="form-control" name="comentario" id="comentario" value="{{ old('comentario', $vinculo->pivot->comentario) }}">
            </div>

            <div class="form-group" style="max-width: 200px;">
                <label for="tamanho">Tamanho (Opcional):</label>
                <div class="input-group">
                    <input type="number" step="0.01" class="form-control" name="tamanho" id="tamanho" placeholder="0.00" value="{{ old('tamanho', $vinculo->pivot->tamanho) }}">
                    <div class="input-group-append">
                        <span class="input-group-text">m</span>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Atualizar Tipo de Porta</button>
                <a href="/patch-panels/{{ $patchPanel->id }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection