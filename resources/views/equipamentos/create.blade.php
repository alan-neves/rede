@extends('main')

@section('content')
<div class="card">
    <div class="card-header bg-usp">
        <div class="d-flex justify-content-between align-items-center">
            <span class="h4 mb-0 text-dark">
                <i class="fas fa-network-wired"></i> Cadastrar Novo Equipamento
            </span>
        </div>
    </div>
    <div class="card-body">
        <form action="/equipamentos" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="rack_id" class="form-label">Rack *</label>
                <select class="form-select" id="rack_id" name="rack_id">
                    <option value="">Selecione um rack</option>
                    @foreach($racks as $rack)
                        <option value="{{ $rack->id }}" 
                            {{ (old('rack_id') ?? $rack_selecionado ?? '') == $rack->id ? 'selected' : '' }}>
                            {{ $rack->nome }} ({{ $rack->predio->nome }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-3">
                <label for="hostname" class="form-label">Hostname *</label>
                <input type="text" class="form-control" 
                       id="hostname" name="hostname" value="{{ old('hostname') }}"
                       placeholder="Ex: switch-01">
            </div>
            
            <div class="mb-3">
                <label for="model" class="form-label">Modelo *</label>
                <input type="text" class="form-control" 
                       id="model" name="model" value="{{ old('model') }}"
                       placeholder="Ex: hp_comware" >
            </div>
            
            <div class="mb-3">
                <label for="ip" class="form-label">IP *</label>
                <input type="text" class="form-control" 
                       id="ip" name="ip" value="{{ old('ip') }}"
                       placeholder="Ex: 192.168.1.1">
            </div>
            
            <div class="mb-3">
                <label for="qtde_portas" class="form-label">Quantidade de Portas *</label>
                <input type="number" class="form-control" 
                       id="qtde_portas" name="qtde_portas" value="{{ old('qtde_portas') }}"
                       min="1" max="48" placeholder="Ex: 24">
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="poe_type" name="poe_type" 
                           value="1" {{ old('poe') ? 'checked' : '' }}>
                    <label class="form-check-label" for="poe_type">
                        Equipamento possui PoE (Power over Ethernet)
                    </label>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Salvar
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection