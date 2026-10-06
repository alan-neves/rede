@extends('main')

@section('content')

@forelse($predios as $predio)
    <h4 class="mt-4 text-uppercase">Prédio: {{ $predio->nome }}</h4>

    <div class="row">
        @forelse($predio->racks as $rack)
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-usp py-2">
                        <strong class="text-dark">{{ $rack->nome }}</strong>
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse($rack->equipamentos as $equipamento)
                            <li class="list-group-item py-1">
                                @can('admin')
                                    <a href="{{ route('equipamentos.show', ['equipamento' => $equipamento]) }}">
                                        {{ $equipamento->hostname }}
                                    </a>
                                @else
                                    {{ $equipamento->hostname }}
                                @endcan
                            </li>
                        @empty
                            <li class="list-group-item py-1 text-muted">Sem equipamentos</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Nenhum rack cadastrado neste prédio.</div>
            </div>
        @endforelse
    </div>
@empty
    <div class="alert alert-info">Nenhum prédio cadastrado ainda.</div>
@endforelse

@endsection('content')