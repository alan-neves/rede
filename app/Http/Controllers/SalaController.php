<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sala;
use App\Models\Predio;
use App\Models\PatchPanel;
use App\Models\Rack;
use App\Models\Planta;
use App\Http\Requests\SalaRequest;
use App\Http\Requests\VincularPortaSalaRequest;
use Illuminate\Support\Facades\Gate;

class SalaController extends Controller
{
    public function create(Request $request)
    {
        Gate::authorize('admin');
        $predios = Predio::all();
        $predio_id = $request->input('predio_id');

        return view('salas.create', [
            'predios' => $predios,
            'predio_selecionado' => $predio_id
        ]);
    }

    public function store(SalaRequest $request)
    {
        Gate::authorize('admin');
        Sala::create($request->validated() + ['user_id' => auth()->id()]);
        session()->flash('alert-success', 'Sala criada com sucesso!');

        return redirect("/predios/{$request->predio_id}");
    }

    public function show(Sala $sala)
    {
        Gate::authorize('admin');
        $patchPanelsVinculados = $sala->patchPanels()
            ->withPivot('porta')
            ->orderBy('patch_panels.nome')
            ->orderBy('porta', 'asc')
            ->paginate(10);

        $racks = $sala->predio->racks;

        return view('salas.show', [
            'sala' => $sala,
            'patchPanels' => $patchPanelsVinculados,
            'racks' => $racks
        ]);
    }

    public function edit(Sala $sala)
    {
        Gate::authorize('admin');
        $predios = Predio::all();

        return view('salas.edit', [
            'sala' => $sala,
            'predios' => $predios,
        ]);
    }

    public function update(SalaRequest $request, Sala $sala)
    {
        Gate::authorize('admin');
        $sala->update($request->validated() + ['user_id' => auth()->id()]);
        session()->flash('alert-success', 'Sala atualizada com sucesso!');

        return redirect("/salas/{$sala->id}");
    }

    public function destroy(Sala $sala)
    {
        Gate::authorize('admin');

        if($sala->patchPanels->isEmpty()){
            $sala->delete();
            session()->flash('alert-success', 'Sala removida com sucesso!');
        } else {
            session()->flash('alert-danger', 'Sala não pode ser removida, pois possui portas vinculadas');
        }
        return back();
    }

    public function markers(Planta $planta)
    {
        Gate::authorize('admin');

        // 1. Salas marcadas NESTA planta
        $salasMarkerd = Sala::where('planta_id', $planta->id)
            ->whereNotNull('x')
            ->whereNotNull('y')
            ->get();

        // 2. Salas do MESMO PRÉDIO pendentes de marcação
        $salasNotMarkerd = Sala::where('predio_id', $planta->predio_id)
            ->whereNull('x')
            ->whereNull('y')
            ->get();

        return view('salas.markers', [
            'planta'          => $planta,
            'salasMarkerd'    => $salasMarkerd,
            'salasNotMarkerd' => $salasNotMarkerd,
        ]);
    }

    public function mark(Sala $sala, Planta $planta, Request $request)
    {
        Gate::authorize('admin');

        $validated = $request->validate([
            'planta_id' => 'required|exists:plantas,id',
            'x'         => 'required|numeric',
            'y'         => 'required|numeric',
            'fontsize'  => 'nullable|integer|min:2|max:50',
            'descricao' => 'nullable|string|max:255', // Adicionada validação da descrição
        ]);

        $dadosAtualizacao = [
            'x'         => $validated['x'],
            'y'         => $validated['y'],
            'planta_id' => $validated['planta_id'],
        ];

        if ($request->has('descricao')) {
            $dadosAtualizacao['descricao'] = $validated['descricao'];
        }

        if ($request->filled('fontsize')) {
            $dadosAtualizacao['fontsize'] = $validated['fontsize'];
        } elseif (is_null($sala->fontsize)) {
            $dadosAtualizacao['fontsize'] = 12;
        }

        $sala->update($dadosAtualizacao);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Posição da sala atualizada com sucesso!'
            ]);
        }

        return redirect()->back()->with('success', 'Sala vinculada à planta com sucesso!');
    }

    public function unmark(Sala $sala)
    {
        Gate::authorize('admin');

        // Zera as coordenadas x e y de todas as marcações vinculadas a esta planta
        $sala->update([
            'x' => null,
            'y' => null,
            'planta_id' => null,
        ]);

        return back()->with('success', 'Sala removido com sucesso!');
    }
}
