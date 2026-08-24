@extends('main')

@section('content')
<div class="card">
    <div class="card-header bg-usp">
        <h1 class="h4 mb-0 text-dark">
            Selecionar Tipo de Porta para Porta {{ $porta }}
            <small class="text-muted d-block">Patch Panel: {{ $patchPanel->nome }} | Sala: {{ $sala->nome }}</small>
        </h1>
    </div>
    <div class="card-body">
        <form action="/patch-panels/{{ $patchPanel->id }}/vincular-sala?porta={{ $porta }}" method="POST">
            @csrf
            <input type="hidden" name="sala_id" value="{{ $sala->id }}">
            <input type="hidden" name="porta" value="{{ $porta }}">
            
            <div class="form-group">
                <label for="tipo_porta_id">Tipo de Porta (Opcional):</label>
                <select class="form-control" name="tipo_porta_id" id="tipo_porta_id">
                    <option value="">-- Não informar tipo --</option>
                    @foreach($tipoPortas as $tipoPorta)
                        <option value="{{ $tipoPorta->id }}">{{ $tipoPorta->nome }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="comentario">Comentário (Opcional):</label>
                <input class="form-control" name="comentario" id="comentario">
            </div>

            <div class="form-group" style="max-width: 200px;">
                <label for="tamanho">Tamanho (Opcional):</label>
                <div class="input-group">
                    <input type="number" step="0.01" class="form-control" name="tamanho" id="tamanho" placeholder="0.00">
                    <div class="input-group-append">
                        <span class="input-group-text">m</span>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Vincular Porta</button>
                <a href="/patch-panels/{{ $patchPanel->id }}/selecionar-sala?porta={{ $porta }}" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
    </div>
</div>
@endsection