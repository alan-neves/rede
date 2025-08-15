@extends('main')

@section('content')
<div class="card">
    <div class="card-header bg-usp">
        <h1 class="h4 mb-0 text-dark">
            Tipo de Vinculação para Porta {{ $porta }}
            <small class="text-muted d-block">Patch Panel: {{ $patchPanel->nome }}</small>
        </h1>
    </div>
    <div class="card-body">
        <div class="list-group">
            <a href="/patch-panels/{{ $patchPanel->id }}/selecionar-sala?porta={{ $porta }}" class="list-group-item list-group-item-action">
                <i class="fas fa-door-open me-2"></i> Vincular a uma Sala
            </a>
            <a href="/link para racks do predio" class="list-group-item list-group-item-action">
                <i class="fas fa-network-wired me-2"></i> Vincular a um Equipamento
            </a>
        </div>
        
        <div class="mt-3">
            <a href="/patch-panels/{{ $patchPanel->id }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>
@endsection