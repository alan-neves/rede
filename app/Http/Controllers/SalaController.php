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

    public function selecionarRack(Sala $sala)
    {
        Gate::authorize('admin');
        $racks = $sala->predio->racks;

        return view('salas.selecionar-rack', [
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

        return view('salas.selecionar-patchpanel', [
            'sala' => $sala,
            'rack' => $rack,
            'patchPanels' => $patchPanelsDisponiveis,
            'selectedPatchPanelId' => $request->patch_panel_id
        ]);
    }

    public function vincularPatchPanel(VincularPortaSalaRequest $request, Sala $sala)
    {
        Gate::authorize('admin');

        $patchPanel = PatchPanel::findOrFail($request->patch_panel_id);
        $portas = array_map('intval', $request->portas ?? []);
        $tiposPorta = $request->tipos_porta ?? [];

        $portasOcupadas = $patchPanel->salasVinculadas()
            ->whereIn('porta', $portas)
            ->pluck('porta')
            ->toArray();

        $portasDisponiveis = array_diff($portas, $portasOcupadas);

        foreach ($portasDisponiveis as $porta) {
            $dadosVinculo = [
                'porta' => $porta,
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Só adiciona tipo_porta_id se foi selecionado para esta porta
            if (!empty($tiposPorta[$porta])) {
                $dadosVinculo['tipo_porta_id'] = $tiposPorta[$porta];
            }

            $sala->patchPanels()->attach($patchPanel->id, $dadosVinculo);
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

    public function editarTipoPorta(Sala $sala, PatchPanel $patchPanel, Request $request)
    {
        Gate::authorize('admin');
        $porta = $request->query('porta');

        // Verificar se existe o vínculo
        $vinculo = $sala->patchPanels()
            ->wherePivot('porta', $porta)
            ->where('patch_panel_id', $patchPanel->id)
            ->first();

        if (!$vinculo) {
            session()->flash('alert-danger', 'Vínculo não encontrado!');
            return redirect("/salas/{$sala->id}");
        }

        return view('salas.editar-tipo-porta', [
            'sala' => $sala,
            'patchPanel' => $patchPanel,
            'porta' => $porta,
            'tipoPortaAtual' => $vinculo->pivot->tipo_porta_id,
            'tipoPortas' => \App\Models\TipoPorta::all()
        ]);
    }

    public function atualizarTipoPorta(Request $request, Sala $sala, PatchPanel $patchPanel)
    {
        Gate::authorize('admin');
        $porta = $request->porta;

        // Validar a requisição
        $request->validate([
            'tipo_porta_id' => 'nullable|exists:tipo_portas,id'
        ]);

        // Atualizar o tipo de porta no vínculo
        $sala->patchPanels()
            ->wherePivot('porta', $porta)
            ->where('patch_panel_id', $patchPanel->id)
            ->updateExistingPivot($patchPanel->id, [
                'tipo_porta_id' => $request->tipo_porta_id,
                'updated_at' => now()
            ]);

        session()->flash('alert-success', 'Tipo de porta atualizado com sucesso!');
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
        $validated = $request->validate([
            'planta_id'           => 'required|exists:plantas,id',
            'x'                   => 'required|numeric',
            'y'                   => 'required|numeric',
            'fontsize'            => 'nullable|integer|min:6|max:50',
        ]);

        // Atualiza a linha existente atribuindo as coordenadas e a planta_id
        $sala->update([
            'x'         => $validated['x'],
            'y'         => $validated['y'],
            'planta_id' => $validated['planta_id'],
            'fontsize' => $request->fontsize ?? 12,
        ]);

        return redirect()->back()->with('success', 'Sala vinculado à planta com sucesso!');
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
