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
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function edit(Planta $planta)
    {
        Gate::authorize('admin');

        // Carrega e ordena os pontos marcados nesta planta
        $pontos = PatchPanelSala::with(['patchPanel.rack', 'sala'])
            ->where('planta_id', $planta->id)
            ->get()
            ->sortBy([
                fn ($a, $b) => (optional(optional($a->patchPanel)->rack)->nome ?? '') <=> (optional(optional($b->patchPanel)->rack)->nome ?? ''),
                fn ($a, $b) => (optional($a->patchPanel)->nome ?? '') <=> (optional($b->patchPanel)->nome ?? ''),
                fn ($a, $b) => ($a->porta ?? 0) <=> ($b->porta ?? 0),
            ])
            ->values(); // Reindexa a coleção

        return view('plantas.edit', [
            'planta' => $planta,
            'pontos' => $pontos,
        ]);
    }

    public function update(PlantaRequest $request, Planta $planta)
    {
        Gate::authorize('admin');

        $planta->name = $request->input('name');
        $planta->predio_id = $request->input('predio_id');
        $planta->public = $request->boolean('public');

        if ($request->hasFile('planta')) {
            if ($planta->path && Storage::exists($planta->path)) {
                Storage::delete($planta->path);
            }

            $file = $request->file('planta');
            $planta->path = $file->store('plantas');
            $planta->original_name = $file->getClientOriginalName();
        }

        $planta->save();

        PatchPanelSala::where('planta_id', $planta->id)->update(['visible' => false]);

        $pontosVisiveis = $request->input('pontos_visiveis', []);
        if (!empty($pontosVisiveis)) {
            PatchPanelSala::whereIn('id', $pontosVisiveis)
                ->where('planta_id', $planta->id)
                ->update(['visible' => true]);
        }

        return redirect("/plantas/{$planta->predio_id}")
            ->with('success', 'Planta e permissões dos pontos atualizadas com sucesso!');
    }

    public function show(Predio $predio, Planta $planta)
    {
        if (!$planta->public) {
            Gate::authorize('admin');
        }
        return Storage::download($planta->path, $planta->original_name);
    }

    public function showPublic(Planta $planta)
    {
        if (!$planta->public) {
            Gate::authorize('admin');
        }

        $planta->load('predio');

        // Carrega os pontos visíveis ordenados por Sala e depois por Rack -> Patch Panel -> Porta
        $markers = PatchPanelSala::where('planta_id', $planta->id)
            ->where('visible', true)
            ->with(['patchPanel.rack', 'sala', 'tipoPorta'])
            ->get()
            ->sortBy([
                fn ($a, $b) => (optional($a->sala)->nome ?? '') <=> (optional($b->sala)->nome ?? ''),
                fn ($a, $b) => (optional(optional($a->patchPanel)->rack)->nome ?? '') <=> (optional(optional($b->patchPanel)->rack)->nome ?? ''),
                fn ($a, $b) => (optional($a->patchPanel)->nome ?? '') <=> (optional($b->patchPanel)->nome ?? ''),
                fn ($a, $b) => ($a->porta ?? 0) <=> ($b->porta ?? 0),
            ])
            ->values();

        return view('plantas.public_show', [
            'planta'  => $planta,
            'markers' => $markers,
        ]);
    }

    public function pdfPublic(Planta $planta)
    {
        if (!$planta->public) {
            Gate::authorize('admin');
        }

        $planta->load('predio');

        // Carrega os pontos visíveis ordenados por Sala -> Rack -> Patch Panel -> Porta
        $markers = PatchPanelSala::where('planta_id', $planta->id)
            ->where('visible', true)
            ->with(['patchPanel.rack', 'sala', 'tipoPorta'])
            ->get()
            ->sortBy([
                fn ($a, $b) => (optional($a->sala)->nome ?? '') <=> (optional($b->sala)->nome ?? ''),
                fn ($a, $b) => (optional(optional($a->patchPanel)->rack)->nome ?? '') <=> (optional(optional($b->patchPanel)->rack)->nome ?? ''),
                fn ($a, $b) => (optional($a->patchPanel)->nome ?? '') <=> (optional($b->patchPanel)->nome ?? ''),
                fn ($a, $b) => ($a->porta ?? 0) <=> ($b->porta ?? 0),
            ])
            ->values();

        $svgBase64 = null;

        if ($planta->path && Storage::exists($planta->path)) {
            $rawSvg = Storage::get($planta->path);

            try {
                // 1. Carrega o SVG como XML Estruturado
                $xml = new \SimpleXMLElement($rawSvg);

                // 2. Extrai ou define as dimensões viewBox do SVG
                $viewBoxWidth = 1000;
                $viewBoxHeight = 1000;

                if (isset($xml['viewBox'])) {
                    $vbParts = preg_split('/[\s,]+/', trim((string)$xml['viewBox']));
                    if (count($vbParts) == 4) {
                        $viewBoxWidth = (float)$vbParts[2];
                        $viewBoxHeight = (float)$vbParts[3];
                    }
                } elseif (isset($xml['width']) && isset($xml['height'])) {
                    $viewBoxWidth = (float) $xml['width'];
                    $viewBoxHeight = (float) $xml['height'];
                }

                // 3. Cria o grupo principal de marcações dentro do nó SVG
                $markersGroup = $xml->addChild('g');
                $markersGroup->addAttribute('id', 'layer-pontos-marcados');

                foreach ($markers as $marker) {
                    // Converte % X/Y para coordenadas reais do SVG
                    $cx = ($marker->x / 100) * $viewBoxWidth;
                    $cy = ($marker->y / 100) * $viewBoxHeight;
                    
                    $nomePonto = optional(optional($marker->patchPanel)->rack)->nome . '-' . optional($marker->patchPanel)->nome . '-' . $marker->porta;
                    $cor = optional($marker->tipoPorta)->cor ?? '#ef4444';
                    
                    $fontSize = ($marker->fontsize ?? 10) * 1.5;
                    $labelPos = $marker->label_position ?? 'right';

                    // Ajuste das coordenadas do texto (direção)
                    $textAnchor = 'start';
                    $dx = 12;
                    $dy = 4;

                    if ($labelPos === 'left') {
                        $textAnchor = 'end';
                        $dx = -12;
                    } elseif ($labelPos === 'top') {
                        $textAnchor = 'middle';
                        $dx = 0;
                        $dy = -14;
                    } elseif ($labelPos === 'bottom') {
                        $textAnchor = 'middle';
                        $dx = 0;
                        $dy = 18;
                    }

                    // Cria o subgrupo do Ponto
                    $g = $markersGroup->addChild('g');
                    $g->addAttribute('transform', "translate({$cx}, {$cy})");

                    // Desenha o Triângulo
                    $polygon = $g->addChild('polygon');
                    $polygon->addAttribute('points', '0,-8 -7,6 7,6');
                    $polygon->addAttribute('fill', $cor);
                    $polygon->addAttribute('stroke', '#ffffff');
                    $polygon->addAttribute('stroke-width', '1.5');

                    // Desenha o Texto do Ponto
                    $text = $g->addChild('text', htmlspecialchars($nomePonto));
                    $text->addAttribute('x', (string)$dx);
                    $text->addAttribute('y', (string)$dy);
                    $text->addAttribute('text-anchor', $textAnchor);
                    $text->addAttribute('font-family', 'Arial, sans-serif');
                    $text->addAttribute('font-size', "{$fontSize}px");
                    $text->addAttribute('font-weight', 'bold');
                    $text->addAttribute('fill', '#000000');
                    $text->addAttribute('stroke', '#ffffff');
                    $text->addAttribute('stroke-width', '2');
                    $text->addAttribute('paint-order', 'stroke fill');
                }

                // 4. Exporta o novo código XML fundido e converte em Base64
                $svgFinal = $xml->asXML();
                $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgFinal);

            } catch (\Exception $e) {
                // Fallback caso o SVG original não seja um XML válido
                $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($rawSvg);
            }
        }

        $pdf = Pdf::loadView('plantas.pdf_show', [
            'planta'    => $planta,
            'markers'   => $markers,
            'svgBase64' => $svgBase64,
            'publicUrl' => url("/plantas/public/{$planta->id}")
        ])
        ->setOption('isRemoteEnabled', true)
        ->setOption('isHtml5ParserEnabled', true)
        ->setPaper('a4', 'portrait');

        return $pdf->stream("planta_{$planta->id}.pdf");
    }

    public function editMark(Planta $planta)
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

        return view('plantas.mark', [
            'planta' => $planta,
            'markers' => $markers,
            'salasMarkerd' => $salasMarkerd,
            'pontosSemMarcacao' => $pontosSemMarcacao,
            'tipoPortas' => $tipoPortas, // Passado para o Blade
        ]);
    }

    public function mark(Planta $planta, Request $request)
    {
        // Tornamos os campos de formulário opcionais no request para requisições via Drag & Drop
        $validated = $request->validate([
            'patch_panel_sala_id' => 'required|exists:patch_panel_sala,id',
            'planta_id'           => 'required|exists:plantas,id',
            'x'                   => 'required|numeric',
            'y'                   => 'required|numeric',
            'fontsize'            => 'nullable|integer|min:2|max:50',
            'label_position'      => 'nullable|in:left,right,top,bottom',
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
        if ($request->filled('label_position')) {
            $dadosParaAtualizar['label_position'] = $request->label_position;
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
