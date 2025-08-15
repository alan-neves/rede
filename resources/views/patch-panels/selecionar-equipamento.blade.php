@extends('main')

@section('content')
<div class="card">
    <div class="card-header bg-usp">
        <h1 class="h4 mb-0 text-dark">
            Vincular Porta da Sala: {{ $sala->nome }}
            <small class="text-muted d-block">Rack: {{ $rack->nome }} - Prédio: {{ $sala->predio->nome }}</small>
        </h1>
    </div>
    <div class="card-body">
        @if($patchPanels->isEmpty())
            <div class="alert alert-info">Nenhum equipamento disponível neste rack.</div>
        @else
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Selecione um equipamento</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @foreach($patchPanels as $pp)
                                    <a href="/salas/{{ $sala->id }}/selecionar-patchpanel/{{ $rack->id }}?patch_panel_id={{ $pp->id }}" 
                                       class="list-group-item list-group-item-action {{ request('patch_panel_id') == $pp->id ? 'active' : '' }}">
                                        {{ $pp->nome }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(request('patch_panel_id'))
                    @php
                        $selectedPP = $patchPanels->firstWhere('id', request('patch_panel_id'));
                        $portasOcupadas = $selectedPP->salasVinculadas->pluck('pivot.porta')->toArray();
                        $portasDisponiveis = array_diff(range(1, $selectedPP->qtde_portas), $portasOcupadas);
                    @endphp
                    <div class="col-md-8">
                        <form action="/salas/{{ $sala->id }}/vincular-patchpanel" method="POST">
                            @csrf
                            <input type="hidden" name="rack_id" value="{{ $rack->id }}">
                            <input type="hidden" name="patch_panel_id" value="{{ $selectedPP->id }}">
                            
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Portas do Patch Panel: {{ $selectedPP->nome }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Selecione as Portas</label>
                                        <div class="row">
                                            @foreach(range(1, $selectedPP->qtde_portas) as $i)
                                                <div class="col-md-3 col-sm-4 col-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                            name="portas[]" 
                                                            id="porta-{{ $i }}" 
                                                            value="{{ $i }}"
                                                            {{ in_array($i, $portasOcupadas) ? 'disabled' : '' }}>
                                                        <label class="form-check-label" for="porta-{{ $i }}">
                                                            Porta {{ $i }}
                                                            @if(in_array($i, $portasOcupadas))
                                                                <small class="text-danger">(Ocupada)</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light">
                                    <div class="d-flex justify-content-between">
                                        <a href="/salas/{{ $sala->id }}/selecionar-rack" class="btn btn-secondary">Voltar</a>
                                        <button type="submit" class="btn btn-primary">Vincular Portas Selecionada</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="col-md-8">
                        <div class="alert alert-info">
                            Selecione um equipamento à esquerda para visualizar suas portas.
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection


essa view também é semelhante a view de vincular sala a patch panel, mas aqui estamos querendo vincular uma porta de um equipamento a uma porta de um patch panel, essa view tá toda errada porque eu copiei a view de salas só pra ter uma ideia de como vai ficar essa, quero que fique semelhante para manter o padrão do sistema mas com as especificidades do vinculo de patch panels para equipamentos 