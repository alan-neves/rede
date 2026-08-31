@extends('main')

@section('content')
<div class="card">
    <div class="card-header bg-usp">
        <span class="h4 mb-0 text-dark"><i class="fas fa-plus"></i> Cadastrar Modelo de Switch</span>
    </div>
    <div class="card-body">
        <form action="/modelo-switches" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fabricante *</label>
                    <input type="text" name="fabricante" class="form-control @error('fabricante') is-invalid @enderror" value="{{ old('fabricante') }}" placeholder="Ex: HP, Cisco, Aruba">
                    @error('fabricante')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome/Modelo *</label>
                    <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome') }}" placeholder="Ex: 1920S 24G">
                    @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Quantidade de Portas *</label>
                    <input type="number" name="qtde_portas" class="form-control @error('qtde_portas') is-invalid @enderror" value="{{ old('qtde_portas') }}" min="1">
                    @error('qtde_portas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Portas PoE</label>
                    <input type="number" name="qtde_portas_poe" class="form-control @error('qtde_portas_poe') is-invalid @enderror" value="{{ old('qtde_portas_poe', 0) }}" min="0">
                    @error('qtde_portas_poe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar</button>
                <a href="/modelo-switches" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection