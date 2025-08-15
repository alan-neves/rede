<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sala;
use App\Models\Predio;
use App\Models\PatchPanel;
use App\Models\Rack;
use App\Http\Requests\SalaRequest;
use App\Http\Requests\VincularPortaRequest;
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

    public function selecionarRack(Sala $sala)
    {
        Gate::authorize('admin');
        $racks = $sala->predio->racks;

        return view('selecionar.selecionar-rack', [
            'sala' => $sala,
            'racks' => $racks
        ]);
    }

    public function selecionarPatchPanel(Sala $sala, Rack $rack, Request $request)
    {
        Gate::authorize('admin');
        $patchPanelsDisponiveis = $rack->patchPanels()
            ->withCount(['salasVinculadas as portas_ocupadas' => function($query) {
                $query->select(\DB::raw('count(distinct porta)'));
            }])
            ->get();

        return view('selecionar.selecionar-patchpanel', [
            'sala' => $sala,
            'rack' => $rack,
            'patchPanels' => $patchPanelsDisponiveis,
            'selectedPatchPanelId' => $request->patch_panel_id
        ]);
    }

    public function vincularPatchPanel(VincularPortaRequest $request, Sala $sala)
    {
        Gate::authorize('admin');

        $patchPanel = PatchPanel::findOrFail($request->patch_panel_id);
        $portas = array_map('intval', $request->portas ?? []);

        $portasOcupadas = $patchPanel->salasVinculadas()
            ->whereIn('porta', $portas)
            ->pluck('porta')
            ->toArray();

        $portasDisponiveis = array_diff($portas, $portasOcupadas);

        foreach ($portasDisponiveis as $porta) {
            $sala->patchPanels()->attach($patchPanel->id, [
                'porta' => $porta,
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        session()->flash('alert-success', 'Portas vinculadas com sucesso!');
        return redirect("/salas/{$sala->id}");
    }

    public function desvincularPatchPanel(Sala $sala, PatchPanel $patchPanel, Request $request)
    {
        Gate::authorize('admin');
        $porta = $request->query('porta');

        $sala->patchPanels()
            ->wherePivot('porta', $porta)
            ->where('patch_panel_id', $patchPanel->id)
            ->detach($patchPanel->id);

        session()->flash('alert-success', 'Porta desvinculada com sucesso!');

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
}
