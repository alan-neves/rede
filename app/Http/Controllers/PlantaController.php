<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Planta;
use App\Http\Requests\PlantaRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Predio;
use Illuminate\Support\Facades\Storage;

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
