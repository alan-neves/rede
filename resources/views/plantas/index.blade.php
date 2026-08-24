@extends('main')

@section('content')

<div class="card">
    <div class="card-header bg-usp">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0 text-dark"> 
                <i class="fas fa-building"></i> {{ $predio->nome }}
            </h1>
            <a href="/" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <div class="card-body">
        @if($predio->descricao)
        <p><strong>Descrição:</strong> {{ $predio->descricao }}</p>
        @endif

        @foreach($predio->plantas as $planta)
            <div class="card mb-4 shadow-sm">
                <!-- Cabeçalho com as ações -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-secondary">{{ $planta->name }}</span>
                    <div>
                        <a href="/salas/{{$planta->id}}/markers" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Marcar Salas
                        </a>
                        <a href="/plantas/{{$planta->id}}/edit" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Marcar planta
                        </a>
                        <form action="/plantas/{{$planta->predio_id}}/{{$planta->id}}" method="post" class="d-inline">
                            @csrf
                            @method('delete')
                            <button type="submit" 
                                    onclick="return confirm('Tem certeza?');" 
                                    class="btn btn-danger btn-sm">
                                Deletar Planta
                            </button> 
                        </form>
                    </div>
                </div>

                <!-- Corpo do card com a Imagem e os Marcadores (Triângulos) -->
                <div class="card-body p-0">
                    <div style="position: relative; width: 100%; display: block;">
                        
                        <!-- Imagem da Planta -->
                        <img src="/plantas/{{$planta->predio_id}}/{{$planta->id}}" 
                             class="img-fluid rounded" 
                             style="width: 100%; height: auto; display: block;" 
                             alt="Planta Baixa">

                        <!-- Marcadores da Planta (Triângulos e Nomes) -->
                        @include('plantas.partials.markers', ['markers' => $planta->markers])
                        <div style="pointer-events: none;">
                            @include('salas.partials.markers', ['salas' => $planta->salas])
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
        @include('plantas.form')
    </div>
</div>

@endsection