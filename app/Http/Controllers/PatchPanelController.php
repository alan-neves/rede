<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatchPanel;
use App\Models\Rack;
use App\Models\Sala;
use App\Http\Requests\PatchPanelRequest;
use App\Http\Requests\VincularPortaPatchPanelRequest;
use Illuminate\Support\Facades\Gate;

class PatchPanelController extends Controller
{
    public function create(Request $request)
    {
        Gate::authorize('admin');
        $racks = Rack::all();
        $rack_id = $request->input('rack_id');

        return view('patch-panels.create', [
            'racks' => $racks,
            'rack_selecionado' => $rack_id
        ]);
    }

    public function store(PatchPanelRequest $request)
    {
        Gate::authorize('admin');
        $patchPanel = PatchPanel::create($request->validated() + ['user_id' => auth()->id()]);
        session()->flash('alert-success', 'Patch panel criado com sucesso!');
        return redirect("/racks/{$patchPanel->rack_id}");
    }

    public function show(PatchPanel $patchPanel)
    {
        Gate::authorize('admin');
        
        $salasVinculadas = $patchPanel->salas()
            ->withPivot('porta', 'tipo_porta_id') 
            ->orderBy('salas.nome')
            ->orderBy('porta', 'asc')
            ->get();

        $salasPredio = Sala::where('predio_id', $patchPanel->rack->predio_id)->get();

        return view('patch-panels.show', [
            'patchPanel' => $patchPanel,
            'salasVinculadas' => $salasVinculadas, 
            'salasPredio' => $salasPredio,
        ]);
    }

    public function edit(PatchPanel $patchPanel)
    {
        Gate::authorize('admin');
        $racks = Rack::all();

        return view('patch-panels.edit', [
            'patchPanel' => $patchPanel,
            'racks' => $racks,
        ]);
    }

    public function update(PatchPanelRequest $request, PatchPanel $patchPanel)
    {
        Gate::authorize('admin');
        $patchPanel->update($request->validated() + ['user_id' => auth()->id()]);
        session()->flash('alert-success', 'Patch panel atualizado com sucesso!');
        return redirect("/patch-panels/{$patchPanel->id}");
    }

    public function selecionarSala(PatchPanel $patchPanel, Request $request)
    {
        Gate::authorize('admin');
        $porta = $request->query('porta');

        $salasPredio = Sala::where('predio_id', $patchPanel->rack->predio_id)->get();

        return view('patch-panels.selecionar-sala', [
            'patchPanel' => $patchPanel,
            'salasPredio' => $salasPredio,
            'porta' => $porta
        ]);
    }

    public function selecionarTipoPorta(PatchPanel $patchPanel, Sala $sala, Request $request)
    {
        Gate::authorize('admin');
        $porta = $request->query('porta');

        return view('patch-panels.selecionar-tipo-porta', [
            'patchPanel' => $patchPanel,
            'sala' => $sala,
            'porta' => $porta,
            'tipoPortas' => \App\Models\TipoPorta::all()
        ]);
    }

    public function vincularSala(VincularPortaPatchPanelRequest $request, PatchPanel $patchPanel)
    {
        Gate::authorize('admin');
        
        $porta = $request->porta ?? $request->query('porta');
        
        // Verificar se a porta já está vinculada
        if ($patchPanel->salas()->wherePivot('porta', $porta)->exists()) {
            session()->flash('alert-danger', 'Esta porta já está vinculada!');
            return redirect("/patch-panels/{$patchPanel->id}");
        }

        $dadosVinculo = [
            'porta' => $porta, 
            'user_id' => auth()->id(),
            'comentario' => $request->comentario,
            'tamanho' => $request->tamanho,
            'tipo_porta_id' => $request->tipo_porta_id // Pode ser null
        ];

        $patchPanel->salas()->attach($request->sala_id, $dadosVinculo);
        
        session()->flash('alert-success', 'Porta vinculada com sucesso!');
        return redirect("/patch-panels/{$patchPanel->id}");
    }

    public function desvincularSala(PatchPanel $patchPanel, Sala $sala, Request $request)
    {
        Gate::authorize('admin');
        $porta = $request->query('porta');

        $patchPanel->salas()->wherePivot('porta', $porta)->detach($sala->id);
        session()->flash('alert-success', 'Porta desvinculada com sucesso!');
        return redirect("/patch-panels/{$patchPanel->id}");
    }

    public function editarTipoPorta(PatchPanel $patchPanel, Sala $sala, Request $request)
    {
        Gate::authorize('admin');
        $porta = $request->query('porta');

        // Verificar se existe o vínculo
        $vinculo = $patchPanel->salas()
            ->wherePivot('porta', $porta)
            ->where('sala_id', $sala->id)
            ->first();

        if (!$vinculo) {
            session()->flash('alert-danger', 'Vínculo não encontrado!');
            return redirect("/patch-panels/{$patchPanel->id}");
        }

        return view('patch-panels.editar-tipo-porta', [
            'patchPanel' => $patchPanel,
            'sala' => $sala,
            'porta' => $porta,
            'vinculo' => $vinculo,
            'tipoPortaAtual' => $vinculo->pivot->tipo_porta_id,
            'tipoPortas' => \App\Models\TipoPorta::all()
        ]);
    }

    public function atualizarTipoPorta(Request $request, PatchPanel $patchPanel, Sala $sala)
    {
        Gate::authorize('admin');
        $porta = $request->porta;

        // Validar a requisição
        $request->validate([
            'tipo_porta_id' => 'nullable|exists:tipo_portas,id'
        ]);

        // Atualizar o tipo de porta no vínculo
        $patchPanel->salas()
            ->wherePivot('porta', $porta)
            ->where('sala_id', $sala->id)
            ->updateExistingPivot($sala->id, [
                'tipo_porta_id' => $request->tipo_porta_id,
                'comentario' => $request->comentario,
                'tamanho' => $request->tamanho,
                'updated_at' => now()
            ]);

        session()->flash('alert-success', 'Tipo de porta atualizado com sucesso!');
        return redirect("/patch-panels/{$patchPanel->id}");
    }

    public function destroy(PatchPanel $patchPanel)
    {
        Gate::authorize('admin');

        if ($patchPanel->salasVinculadas->isEmpty()) {
            $patchPanel->delete();
            session()->flash('alert-success', 'Patch panel removido com sucesso');
        } else {
            session()->flash('alert-danger', 'Não foi possível deletar, pois existem portas vinculadas');
        }
        return back();
    }
}
