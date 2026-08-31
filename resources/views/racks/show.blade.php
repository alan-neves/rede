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
                        @if($rack->equipamentos->isEmpty())
                        <div class="alert alert-info">Nenhum equipamento cadastrado neste rack.</div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 30px;"></th>
                                        <th>Hostname / Modelo</th>
                                        <th width="180px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-equipamentos" data-rack-id="{{ $rack->id }}">
                                    @foreach($rack->equipamentos as $equipamento)
                                    <tr data-id="{{ $equipamento->id }}">
                                        <td class="align-middle" style="cursor: grab;">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
                                        <td>
                                            <strong>{{ $equipamento->hostname }}</strong><br>
                                            <small class="text-muted">{{ $equipamento->modeloSwitch?->nome ?? 'Sem modelo' }}</small><br>
                                            <span class="badge bg-{{ $equipamento->cor_tipo }}">{{ $equipamento->tipo_label }}</span>
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
                        @if($rack->patchPanels->isEmpty())
                        <div class="alert alert-info">Nenhum patch panel cadastrado neste rack.</div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 30px;"></th>
                                        <th>Identificação / Portas</th>
                                        <th width="180px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-patchpanels" data-rack-id="{{ $rack->id }}">
                                    @foreach($rack->patchPanels as $patchPanel)
                                    <tr data-id="{{ $patchPanel->id }}">
                                        <td class="align-middle" style="cursor: grab;">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
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

        <!-- Seção dos desenhos -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h3 class="h6 mb-0">Diagrama de Equipamentos</h3>
                    </div>
                    <div class="card-body">
                        @include('racks.partials.equipment')
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h3 class="h6 mb-0">Diagrama de Patch Panels</h3>
                    </div>
                    <div class="card-body">
                        @include('racks.partials.patch_panel')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascripts_bottom')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
function initTableSortable(tbodyId, tipo) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody || tbody.children.length === 0) return;

    const rackId = tbody.getAttribute('data-rack-id');

    new Sortable(tbody, {
        handle: 'td:first-child',
        animation: 150,
        ghostClass: 'table-active',
        onEnd: function() {
            const itens = [];
            tbody.querySelectorAll('tr[data-id]').forEach((row, index) => {
                itens.push({
                    tipo: tipo,
                    id: row.getAttribute('data-id'),
                    ordem: index + 1
                });
            });

            fetch('/racks/' + rackId + '/reordenar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ itens: itens })
            })
            .then(r => {
                if (!r.ok) throw new Error('Erro ao salvar');
                return r.json();
            })
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            })
            .catch(err => {
                alert('Erro ao reordenar: ' + err.message);
                window.location.reload();
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initTableSortable('tbody-equipamentos', 'equipamento');
    initTableSortable('tbody-patchpanels', 'patchpanel');
});
</script>
@endsection