@extends('main')

@section('content')
<div class="card">
    <div class="card-header bg-usp">
        <span class="h4 mb-0 text-dark"><i class="fas fa-network-wired"></i> Cadastrar Novo Equipamento</span>
    </div>
    <div class="card-body">
        <form action="/equipamentos" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Rack *</label>
                    <select name="rack_id" class="form-select @error('rack_id') is-invalid @enderror">
                        <option value="">Selecione um rack</option>
                        @foreach($racks as $rack)
                            <option value="{{ $rack->id }}" {{ (old('rack_id') ?? $rack_selecionado ?? '') == $rack->id ? 'selected' : '' }}>
                                {{ $rack->nome }} ({{ $rack->predio->nome }})
                            </option>
                        @endforeach
                    </select>
                    @error('rack_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Modelo *</label>
                    <select name="modelo_switch_id" id="modelo_switch_id" class="form-select @error('modelo_switch_id') is-invalid @enderror">
                        <option value="">Selecione um modelo</option>
                        @foreach($modelos->groupBy('fabricante') as $fab => $lista)
                            <optgroup label="{{ $fab }}">
                                @foreach($lista as $m)
                                    <option value="{{ $m->id }}" data-portas="{{ $m->qtde_portas }}" data-poe="{{ $m->qtde_portas_poe }}" {{ old('modelo_switch_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->nome }} ({{ $m->qtde_portas }}p{{ $m->qtde_portas_poe > 0 ? ', '.$m->qtde_portas_poe.' PoE' : '' }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('modelo_switch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div id="info-modelo" class="form-text text-muted"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Hostname *</label>
                    <input type="text" name="hostname" class="form-control @error('hostname') is-invalid @enderror" value="{{ old('hostname') }}" placeholder="Patrimônio">
                    @error('hostname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">IP *</label>
                    <input type="text" name="ip" class="form-control @error('ip') is-invalid @enderror" value="{{ old('ip') }}" placeholder="Ex: 192.168.1.10">
                    @error('ip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo *</label>
                    <select name="tipo" class="form-select @error('tipo') is-invalid @enderror">
                        <option value="">Selecione...</option>
                        <option value="A" {{ old('tipo') == 'A' ? 'selected' : '' }}>A — Acesso</option>
                        <option value="W" {{ old('tipo') == 'W' ? 'selected' : '' }}>W — Wireless</option>
                        <option value="C" {{ old('tipo') == 'C' ? 'selected' : '' }}>C — Câmera</option>
                        <option value="V" {{ old('tipo') == 'V' ? 'selected' : '' }}>V — VoIP</option>
                    </select>
                    @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Comentário</label>
                <textarea name="comentario" class="form-control" rows="2">{{ old('comentario') }}</textarea>
            </div>
            <input type="hidden" name="ordem" value="0">
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('javascripts_bottom')
<script>
document.getElementById('modelo_switch_id').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const portas = opt.getAttribute('data-portas');
    const poe = opt.getAttribute('data-poe');
    document.getElementById('info-modelo').textContent = portas 
        ? `Este modelo possui ${portas} portas${poe > 0 ? ' sendo ' + poe + ' com PoE' : ''}.`
        : '';
});
document.getElementById('modelo_switch_id').dispatchEvent(new Event('change'));
</script>
@endsection