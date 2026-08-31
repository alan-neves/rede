@extends('main')

@section('content')
<div class="card">
    <div class="card-header bg-usp d-flex justify-content-between align-items-center">
        <span class="h4 mb-0 text-dark">
            <i class="fas fa-network-wired"></i> {{ $equipamento->hostname }}
            <span class="badge bg-{{ $equipamento->cor_tipo }}">{{ $equipamento->tipo_label }}</span>
        </span>
        <a href="/racks/{{ $equipamento->rack_id }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Hostname:</strong> {{ $equipamento->hostname }}</li>
                    <li class="list-group-item"><strong>IP:</strong> {{ $equipamento->ip }}</li>
                    <li class="list-group-item"><strong>Modelo:</strong> {{ $equipamento->modeloSwitch?->nome ?? '-' }}</li>
                    <li class="list-group-item"><strong>Fabricante:</strong> {{ $equipamento->modeloSwitch?->fabricante ?? '-' }}</li>
                    <li class="list-group-item"><strong>Portas:</strong> {{ $equipamento->qtde_portas }}</li>
                    <li class="list-group-item"><strong>Portas PoE:</strong> {{ $equipamento->qtde_portas_poe > 0 ? $equipamento->qtde_portas_poe : 'Não' }}</li>
                    <li class="list-group-item"><strong>Tipo:</strong> {{ $equipamento->tipo_label }}</li>
                    <li class="list-group-item"><strong>Rack:</strong> {{ $equipamento->rack->nome }} ({{ $equipamento->rack->predio->nome }})</li>
                    @if($equipamento->comentario)
                        <li class="list-group-item"><strong>Comentário:</strong> {{ $equipamento->comentario }}</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Portas ({{ $equipamento->qtde_portas }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th width="100px">Porta</th><th>Status</th><th width="180px">Ações</th></tr>
                        </thead>
                        <tbody>
                            @for($i = 1; $i <= $equipamento->qtde_portas; $i++)
                            <tr>
                                <td><strong>{{ $i }}</strong></td>
                                <td><span class="badge bg-secondary">Livre</span></td>
                                <td><span class="text-muted">—</span></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection