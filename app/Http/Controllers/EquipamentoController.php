<?php

namespace App\Http\Controllers;

use App\Models\Equipamento;
use App\Models\Predio;
use App\Models\Rack;
use App\Http\Requests\EquipamentoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EquipamentoController extends Controller
{
    public function create(Request $request)
    {
        Gate::authorize('admin');
        return view('equipamentos.create', [
            'racks' => Rack::all(),
            'predios' => Predio::all(),
            'rack_selecionado' => $request->input('rack_id')
        ]);
    }

    public function store(EquipamentoRequest $request)
    {
        Gate::authorize('admin');
        $equipamento = Equipamento::create($request->validated() + ['user_id' => auth()->id()]);
        
        session()->flash('alert-success', 'Equipamento criado com sucesso!');
        return redirect("/racks/{$equipamento->rack_id}");
    }

    public function show(Equipamento $equipamento)
    {
        Gate::authorize('admin');
        return view('equipamentos.show', [
            'equipamento' => $equipamento,
            'portas' => $equipamento->portas()->orderBy('porta')->get()
        ]);
    }

    public function edit(Equipamento $equipamento)
    {
        Gate::authorize('admin');
        return view('equipamentos.edit', [
            'equipamento' => $equipamento,
            'predios' => Predio::all(),
            'racks' => Rack::all()
        ]);
    }

    public function update(EquipamentoRequest $request, Equipamento $equipamento)
    {
        Gate::authorize('admin');
        $equipamento->update($request->validated() + ['user_id' => auth()->id()]);
        
        session()->flash('alert-success', 'Equipamento atualizado com sucesso!');
        return redirect("/equipamentos/{$equipamento->id}");
    }
    
    public function destroy(Equipamento $equipamento)
    {
        Gate::authorize('admin');
        $rack_id = $equipamento->rack_id;
        $equipamento->delete();
        
        session()->flash('alert-success', 'Equipamento removido com sucesso!');
        return redirect("/racks/{$rack_id}");
    }
}