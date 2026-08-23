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

        @include('plantas.form')

        @foreach($predio->plantas as $planta)
            <div class="card mb-4 shadow-sm">
                <!-- Cabeçalho com o botão alinhado à direita -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-secondary">{{ $planta->name }}</span>
                    <a href="/plantas/{{$planta->id}}/edit" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Marcar planta
                    </a>
                    <form action="/plantas/{{$planta->predio_id}}/{{$planta->id}}" method="post" class="m-0">
                        @csrf
                        @method('delete')
                        <button type="submit" 
                                onclick="return confirm('Tem certeza?');" 
                                class="btn btn-danger btn-sm">
                            Deletar Planta
                        </button> 
                    </form>
                </div>

                <!-- Corpo do card com a imagem -->
                <div class="card-body text-center p-2">
                    <img src="/plantas/{{$planta->predio_id}}/{{$planta->id}}" 
                        class="img-fluid rounded" 
                        style="width: 100%; object-fit: contain;">
                </div>
            </div>
        @endforeach
    </div>

@endsection