@extends('main')

@section('content')
<div class="card">
    <div class="card-header">
        <h1>Cadastrar Novo Tipo de Porta</h1>
    </div>
    <div class="card-body">
        <form action="/tipo-portas" method="POST">
            @csrf
            
            {{-- Campo Nome --}}
            <div class="mb-3">
                <label for="nome" class="form-label">Nome *</label>
                <input type="text" 
                       class="form-control @error('nome') is-invalid @enderror" 
                       id="nome" 
                       name="nome" 
                       value="{{ old('nome') }}" 
                       required>
            </div>

            {{-- Campo Cor --}}
            <div class="mb-3">
                <label for="cor" class="form-label">Cor da Marcação</label>
                <div class="input-group">
                    <input type="color" 
                           class="form-control form-control-color" 
                           id="cor_picker" 
                           value="{{ old('cor', '#FF0000') }}" 
                           title="Escolha a cor"
                           onchange="document.getElementById('cor').value = this.value">
                    
                    <input type="text" 
                           class="form-control @error('cor') is-invalid @enderror" 
                           id="cor" 
                           name="cor" 
                           value="{{ old('cor', '#FF0000') }}" 
                           placeholder="#FF0000"
                           maxlength="7"
                           onchange="document.getElementById('cor_picker').value = this.value">
                </div>
                <div class="form-text">Cor utilizada para identificar o marcador no mapa (Padrão: Vermelho).</div>
            </div>
            
            {{-- Ações --}}
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">Salvar</button>
                <a href="/tipo-portas" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection