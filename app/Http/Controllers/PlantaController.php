<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Planta;
use App\Http\Requests\PlantaRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Predio;
use Illuminate\Support\Facades\Storage;
use App\Models\PatchPanelSala;

class PlantaController extends Controller
{
    public function index(Predio $predio)
    {
        Gate::authorize('admin');
        return view('plantas.index',[
            'predio' => $predio,
         ]);
    }


    public function store(PlantaRequest $request, Predio $predio)
    {
        Gate::authorize('admin');
        $file = $request->file('planta');
        $path = $file->store('plantas');

        $planta = new Planta();
        $planta->predio_id = $request->input('predio_id');
        $planta->path = $path;
        $planta->name = $request->input('name');
        $planta->original_name = $file->getClientOriginalName();
        $planta->save();

        return redirect()->back()->with('success', 'Planta enviada com sucesso!');
    }

    public function show(Predio $predio, Planta $planta)
    {
        Gate::authorize('admin');
        return Storage::download($planta->path, $planta->original_name);
    }

    public function edit(Planta $planta)
    {
        Gate::authorize('admin');

        $predio_id = $planta->predio_id;

        // pontos já marcados na planta (com coordenadas x e y)
        $markers = PatchPanelSala::with(['patchPanel.rack', 'sala'])
            ->where('planta_id', $planta->id)
            ->whereNotNull('x')
            ->whereNotNull('y')
            ->get();

        // pontos do MESMO PRÉDIO que ainda estão sem coordenadas (x e y nulos)
        $pontosSemMarcacao = PatchPanelSala::with(['patchPanel.rack', 'sala'])
            ->whereHas('sala', function ($query) use ($predio_id) {
                $query->where('predio_id', $predio_id);
            })
            ->whereNull('x')
            ->whereNull('y')
            ->get();

        return view('plantas.edit', [
            'planta' => $planta,
            'markers' => $markers,
            'pontosSemMarcacao' => $pontosSemMarcacao,
        ]);
    }

    public function update(Planta $planta, Request $request)
    {
        $validated = $request->validate([
            'patch_panel_sala_id' => 'required|exists:patch_panel_sala,id',
            'planta_id'           => 'required|exists:plantas,id',
            'x'                   => 'required|numeric',
            'y'                   => 'required|numeric',
        ]);

        // Atualiza a linha existente atribuindo as coordenadas e a planta_id
        $ponto = PatchPanelSala::findOrFail($validated['patch_panel_sala_id']);
        $ponto->update([
            'x'         => $validated['x'],
            'y'         => $validated['y'],
            'planta_id' => $validated['planta_id'],
        ]);

        return redirect()->back()->with('success', 'Ponto vinculado à planta com sucesso!');
    }

    public function destroy(Predio $predio, Planta $planta)
    {
        Gate::authorize('admin');
        if ($planta->path && Storage::exists($planta->path)) {
            Storage::delete($planta->path);
        }
        $planta->delete();
        return back();
    }
}
