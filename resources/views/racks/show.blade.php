@extends('main')

@section('content')

<br>

<div class="card">
    <div class="card-header bg-usp">
        <div class="d-flex justify-content-between align-items-center">
            <span class="h4 mb-0 text-dark">
                <i class="fas fa-server"></i> Rack: {{ $rack->nome }}
                <small class="text-muted">{{ $rack->predio->nome }}</small>
            </span>
            <a href="/predios/{{ $rack->predio->id }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="row">

            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Equipamentos</h2>
                        @can('user')
                        <a href="/equipamentos/create?rack_id={{ $rack->id }}" class="btn btn-success btn-sm"> 
                        <i class="fas fa-plus"></i> Novo 
                        </a> 
                        @endcan
                    </div>
                    <div class="card-body">
                        @if($equipamentos->isEmpty())
                        <div class="alert alert-info">Nenhum equipamento cadastrado neste rack.</div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Hostname / Modelo</th>
                                        <th width="180px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipamentos as $equipamento)
                                    <tr>
                                        <td>
                                            <strong>{{ $equipamento->hostname }}</strong><br>
                                            <small class="text-muted">{{ $equipamento->model }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="/equipamentos/{{ $equipamento->id }}" class="btn btn-info btn-sm">
                                                    Ver
                                                </a>
                                                <a href="/equipamentos/{{ $equipamento->id }}/edit" class="btn btn-warning btn-sm">
                                                    Editar
                                                </a>
                                                <form action="/equipamentos/{{ $equipamento->id }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este equipamento?')">
                                                        Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Patch Panels</h2>
                        @can('user')
                        <a href="/patch-panels/create?rack_id={{ $rack->id }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                        @endcan
                    </div>
                    <div class="card-body">
                        @if($patchPanels->isEmpty())
                        <div class="alert alert-info">Nenhum patch panel cadastrado neste rack.</div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Identificação / Portas</th>
                                        <th width="180px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patchPanels as $patchPanel)
                                    <tr>
                                        <td>
                                            <strong>{{ $patchPanel->nome }}</strong><br>
                                            <small class="text-muted">{{ $patchPanel->qtde_portas }} portas</small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="/patch-panels/{{ $patchPanel->id }}" class="btn btn-info btn-sm">
                                                    Ver
                                                </a>
                                                <a href="/patch-panels/{{ $patchPanel->id }}/edit" class="btn btn-warning btn-sm">
                                                    Editar
                                                </a>
                                                <form action="/patch-panels/{{ $patchPanel->id }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este patch panel?')">
                                                        Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
