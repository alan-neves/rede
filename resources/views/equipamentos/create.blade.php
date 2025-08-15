@extends('main')

@section('content')
<div class="card">
    <div class="card-header">
        <h1>Cadastrar Novo Equipamento</h1>
    </div>
    <div class="card-body">
        <form action="/equipamentos" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="hostname" class="form-label">Hostname *</label>
                        <input type="text" class="form-control @error('hostname') is-invalid @enderror" 
                               id="hostname" name="hostname" value="{{ old('hostname') }}">
                        @error('hostname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="ip" class="form-label">IP *</label>
                        <input type="text" class="form-control @error('ip') is-invalid @enderror" 
                               id="ip" name="ip" value="{{ old('ip') }}">
                        @error('ip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="model" class="form-label">Modelo *</label>
                        <select class="form-select @error('model') is-invalid @enderror" id="model" name="model">
                            <option value="">Selecione...</option>
                            @foreach(App\Models\Equipamento::model as $model)
                                <option value="{{ $model }}" {{ old('model') == $model ? 'selected' : '' }}>
                                    {{ $model }}
                                </option>
                            @endforeach
                        </select>
                        @error('model')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="poe_type" class="form-label">Tipo POE *</label>
                        <select class="form-select @error('poe_type') is-invalid @enderror" id="poe_type" name="poe_type">
                            <option value="">Selecione...</option>
                            <option value="poe" {{ old('poe_type') == 'poe' ? 'selected' : '' }}>POE</option>
                            <option value="poe+" {{ old('poe_type') == 'poe+' ? 'selected' : '' }}>POE+</option>
                            <option value="none" {{ old('poe_type') == 'none' ? 'selected' : '' }}>Não POE</option>
                        </select>
                        @error('poe_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="qtde_portas" class="form-label">Quantidade de Portas *</label>
                        <input type="number" class="form-control @error('qtde_portas') is-invalid @enderror" 
                               id="qtde_portas" name="qtde_portas" min="1" value="{{ old('qtde_portas') }}">
                        @error('qtde_portas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="patrimonio" class="form-label">Patrimônio</label>
                        <input type="text" class="form-control @error('patrimonio') is-invalid @enderror" 
                               id="patrimonio" name="patrimonio" value="{{ old('patrimonio') }}">
                        @error('patrimonio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="predio_id" class="form-label">Prédio</label>
                        <select class="form-select @error('predio_id') is-invalid @enderror" id="predio_id" name="predio_id">
                            <option value="">Selecione um prédio</option>
                            @foreach($predios as $predio)
                                <option value="{{ $predio->id }}" 
                                    {{ old('predio_id') == $predio->id ? 'selected' : '' }}>
                                    {{ $predio->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('predio_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="rack_id" class="form-label">Rack</label>
                        <select class="form-select @error('rack_id') is-invalid @enderror" id="rack_id" name="rack_id">
                            <option value="">Selecione um rack</option>
                            @foreach($racks as $rack)
                                <option value="{{ $rack->id }}" 
                                    {{ (old('rack_id') ?? $rack_selecionado ?? '') == $rack->id ? 'selected' : '' }}>
                                    {{ $rack->nome }} ({{ $rack->predio->nome }})
                                </option>
                            @endforeach
                        </select>
                        @error('rack_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">Salvar</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection