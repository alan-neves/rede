<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rack;
use App\Models\Predio;
use App\Models\PatchPanel;
use App\Models\Equipamento;
use App\Http\Requests\RackRequest;
use Illuminate\Support\Facades\Gate;

class RackController extends Controller
{
    public function create(Request $request)
    {
        Gate::authorize('admin');
        $predios = Predio::all();
        $predio_id = $request->input('predio_id');

        return view('racks.create', [
            'predios' => $predios,
            'predio_selecionado' => $predio_id
        ]);
    }

    public function store(RackRequest $request)
    {
        Gate::authorize('admin');
        Rack::create($request->validated() + ['user_id' => auth()->id()]);
        session()->flash('alert-success', 'Rack criado com sucesso!');

        return redirect("/predios/{$request->predio_id}");
    }

    public function show(Rack $rack)
    {
        Gate::authorize('admin');

        return view('racks.show', [
            'rack' => $rack
        ]);
    }

    public function edit(Rack $rack)
    {
        Gate::authorize('admin');
        $predios = Predio::all();

        return view('racks.edit', [
            'rack' => $rack,
            'predios' => $predios,
        ]);
    }

    public function update(RackRequest $request, Rack $rack)
    {
        Gate::authorize('admin');
        $rack->update($request->validated() + ['user_id' => auth()->id()]);
        session()->flash('alert-success', 'Rack atualizado com sucesso!');

        return redirect("/racks/{$rack->id}");
    }

    public function destroy(Rack $rack)
    {
        Gate::authorize('admin');

        if ($rack->patchPanels->isEmpty() && $rack->equipamentos->isEmpty()){
            $rack->delete();
            session()->flash('alert-success', 'Rack deletado com sucesso');
        } else {
            session()->flash('alert-danger', 'Não foi possível deletar, pois existem patch panels cadastrados neste rack');
        }
        return redirect()->back();
    }

    public function reordenar(Rack $rack, Request $request)
    {
        Gate::authorize('admin');

        $request->validate([
            'itens' => 'required|array',
            'itens.*.tipo' => 'required|in:equipamento,patchpanel',
            'itens.*.id' => 'required|integer',
            'itens.*.ordem' => 'required|integer',
        ]);

        foreach ($request->itens as $item) {
            if ($item['tipo'] === 'equipamento') {
                Equipamento::where('id', $item['id'])->where('rack_id', $rack->id)
                    ->update(['ordem' => $item['ordem']]);
            } else {
                PatchPanel::where('id', $item['id'])->where('rack_id', $rack->id)
                    ->update(['ordem' => $item['ordem']]);
            }
        }

        return response()->json(['success' => true]);
    }
}
