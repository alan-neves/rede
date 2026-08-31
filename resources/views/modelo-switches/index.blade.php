@extends('main')

@section('content')
<div class="card">
    <div class="card-header bg-usp d-flex justify-content-between align-items-center">
        <span class="h4 mb-0 text-dark"><i class="fas fa-microchip"></i> Modelos de Switch</span>
        <a href="/modelo-switches/create" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Novo Modelo</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fabricante</th>
                        <th>Modelo</th>
                        <th>Portas</th>
                        <th>PoE</th>
                        <th width="220px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modelos as $modelo)
                    <tr>
                        <td><strong>{{ $modelo->fabricante }}</strong></td>
                        <td>{{ $modelo->nome }}</td>
                        <td>{{ $modelo->qtde_portas }}</td>
                        <td>{{ $modelo->qtde_portas_poe > 0 ? $modelo->qtde_portas_poe : '-' }}</td>
                        <td>
                            <a href="/modelo-switches/{{ $modelo->id }}/edit" class="btn btn-warning btn-sm">Editar</a>
                            <form action="/modelo-switches/{{ $modelo->id }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este local?')">
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection