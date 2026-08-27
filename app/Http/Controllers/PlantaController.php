<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Planta;
use App\Models\Sala;
use App\Http\Requests\PlantaRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Predio;
use Illuminate\Support\Facades\Storage;
use App\Models\PatchPanelSala;
use App\Models\TipoPorta;

class PlantaController extends Controller
{
    public function index(Predio $predio)
    {
        Gate::authorize('admin');

        $predio->load([
            'plantas.markers' => function ($query) {
                $query->whereNotNull('x')->whereNotNull('y')->with(['patchPanel.rack', 'sala']);
            },
            // Carrega apenas as salas que possuem marcação (x e y preenchidos)
            'plantas.salas' => function ($query) {
                $query->whereNotNull('x')->whereNotNull('y');
            }
        ]);
        
        return view('plantas.index', [
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

        $salasMarkerd = Sala::where('planta_id', $planta->id)
            ->whereNotNull('x')
            ->whereNotNull('y')
            ->get();

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
            ->get()
            ->sortBy([
                fn ($a, $b) => (optional(optional($a->patchPanel)->rack)->nome ?? '') <=> (optional(optional($b->patchPanel)->rack)->nome ?? ''),
                fn ($a, $b) => (optional($a->patchPanel)->nome ?? '') <=> (optional($b->patchPanel)->nome ?? ''),
                fn ($a, $b) => ($a->porta ?? 0) <=> ($b->porta ?? 0),
            ])
            ->values(); // Reindexa a coleção

        // Busca todos os tipos de porta disponíveis
        $tipoPortas = TipoPorta::all();

        return view('plantas.edit', [
            'planta' => $planta,
            'markers' => $markers,
            'salasMarkerd' => $salasMarkerd,
            'pontosSemMarcacao' => $pontosSemMarcacao,
            'tipoPortas' => $tipoPortas, // Passado para o Blade
        ]);
    }

    public function update(Planta $planta, Request $request)
    {
        // Tornamos os campos de formulário opcionais no request para requisições via Drag & Drop
        $validated = $request->validate([
            'patch_panel_sala_id' => 'required|exists:patch_panel_sala,id',
            'planta_id'           => 'required|exists:plantas,id',
            'x'                   => 'required|numeric',
            'y'                   => 'required|numeric',
            'fontsize'            => 'nullable|integer|min:2|max:50',
        ]);

        $ponto = PatchPanelSala::findOrFail($validated['patch_panel_sala_id']);

        // Monta o array com as coordenadas enviadas
        $dadosParaAtualizar = [
            'x'         => $validated['x'],
            'y'         => $validated['y'],
            'planta_id' => $validated['planta_id'],
        ];

        // Atualiza os outros campos SOMENTE se eles estiverem presentes no request
        if ($request->has('fontsize')) {
            $dadosParaAtualizar['fontsize'] = $request->fontsize;
        }
        if ($request->has('tipo_porta_id')) {
            $dadosParaAtualizar['tipo_porta_id'] = $request->tipo_porta_id;
        }
        if ($request->has('comentario')) {
            $dadosParaAtualizar['comentario'] = $request->comentario;
        }
        if ($request->has('tamanho')) {
            $dadosParaAtualizar['tamanho'] = $request->tamanho;
        }

        // Executa a atualização sem alterar os dados não enviados
        $ponto->update($dadosParaAtualizar);

        // Retorna JSON para chamadas AJAX/Fetch (Drag & Drop)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Posição atualizada com sucesso!',
                'data'    => $ponto
            ], 200);
        }

        return redirect()->back()->with('success', 'Ponto atualizado com sucesso!');
    }

    public function destroy(Predio $predio, Planta $planta)
    {
        Gate::authorize('admin');

        if (PatchPanelSala::where('planta_id', $planta->id)->get()->isNotEmpty()) {
            session()->flash('alert-danger', 'Planta não pode ser removida, pois tem pontos marcados!');
            return back();
        }

        if (Sala::where('planta_id', $planta->id)->get()->isNotEmpty()) {
            session()->flash('alert-danger', 'Planta não pode ser removida, pois tem salas marcadas!');
            return back();
        }

        // Remove o arquivo da imagem do storage
        if ($planta->path && Storage::exists($planta->path)) {
            Storage::delete($planta->path);
        }

        // Deleta o registro da planta
        $planta->delete();
        return back()->with('success', 'Planta e marcações removidas com sucesso!');
    }

    public function unmark($patch_panel_sala_id)
    {
        Gate::authorize('admin');

        // Zera as coordenadas x e y de todas as marcações vinculadas a esta planta
        PatchPanelSala::where('id', $patch_panel_sala_id)->update([
            'x' => null,
            'y' => null,
            'planta_id' => null,
        ]);

        return back()->with('success', 'Ponto removido com sucesso!');
    }
}
