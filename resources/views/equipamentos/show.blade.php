@extends('main')

@section('content')
<div class="card">
    <div class="card-header bg-usp">
        <div class="d-flex justify-content-between align-items-center">
            <span class="h4 mb-0 text-dark">
                <i class="fas fa-network-wired"></i> Equipamento: {{ $equipamento->hostname }}
                <small class="text-muted">
                   {{ $equipamento->model }} | 
                    @if($equipamento->rack)
                        Rack: {{ $equipamento->rack->nome }} | Prédio: {{ $equipamento->rack->predio->nome }}
                    @else
                        Não instalado em rack
                    @endif
                </small>
            </span>
            <div>
                <a href="/racks/{{ $equipamento->rack_id ?? '' }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <!-- Coluna de informações -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Informações</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Hostname:</strong> {{ $equipamento->hostname }}
                            </li>
                            <li class="list-group-item">
                                <strong>Modelo:</strong> {{ $equipamento->model }}
                            </li>
                            <li class="list-group-item">
                                <strong>IP:</strong> {{ $equipamento->ip }}
                            </li>
                            <li class="list-group-item">
                                <strong>Tipo POE:</strong> {{ ucfirst($equipamento->poe_type) }}
                            </li>
                            <li class="list-group-item">
                                <strong>Patrimônio:</strong> {{ $equipamento->patrimonio ?? 'Não informado' }}
                            </li>
                            <li class="list-group-item">
                                <strong>Prédio:</strong> {{ $equipamento->rack->predio->nome ?? 'Não informado' }}
                            </li>
                            <li class="list-group-item">
                                <strong>Rack:</strong> {{ $equipamento->rack->nome ?? 'Não informado' }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Coluna de portas -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Portas ({{ $equipamento->qtde_portas }})</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th width="80px">Porta</th>
                                        <th width="100px">Tipo</th>
                                        <th>Status</th>
                                        <th>Vinculada a</th>
                                        <th width="180px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(range(1, $equipamento->qtde_portas) as $portaNumero)
                                    @php
                                        $porta = $equipamento->portas->where('porta', $portaNumero)->first();
                                        $vinculo = $porta ? $porta->patchPanelPorta : null;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $portaNumero }}</strong></td>
                                        <td>{{ $porta->tipo ?? '-' }}</td>
                                        <td>
                                            @if($vinculo)
                                                <span class="badge bg-success">Vinculada</span>
                                            @else
                                                <span class="badge bg-secondary">Livre</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($vinculo)
                                                {{ $vinculo->patchPanel->nome }} (Porta {{ $vinculo->porta }})
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @can('user')
                                            @if($vinculo)
                                                <form action="/equipamentos/{{ $equipamento->id }}/desvincular-porta/{{ $portaNumero }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja desvincular esta porta?')">
                                                        Desvincular
                                                    </button>
                                                </form>
                                            @else
                                                <a href="/equipamentos/{{ $equipamento->id }}/selecionar-rack?porta={{ $portaNumero }}" class="btn btn-primary btn-sm">
                                                    Vincular
                                                </a>
                                            @endif
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection