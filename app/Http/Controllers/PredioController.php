<?php

namespace App\Http\Controllers;

use App\Models\Predio;
use App\Http\Requests\PredioRequest;
use Illuminate\Support\Facades\Gate;

class PredioController extends Controller
{
    public function index()
    {
        Gate::authorize('admin');
        return view('predios.index',[
            'predios' => Predio::all(),
         ]);
    }

    public function create()
    {
        Gate::authorize('admin');
        return view('predios.create');
    }

    public function store(PredioRequest $request)
    {
        Gate::authorize('admin');
        Predio::create($request->validated() + ['user_id' => auth()->id()]);
        session()->flash('alert-success', 'Prédio criado com sucesso!');
        return redirect('/predios');
    }

    public function show(Predio $predio)
    {
        Gate::authorize('admin');
        return view('predios.show', [
            'predio' => $predio,
            'racks' => $predio->racks,
            'salas' => $predio->salas,
        ]);
    }

    public function edit(Predio $predio)
    {
        Gate::authorize('admin');
        return view('predios.edit', ['predio' => $predio]);
    }

    public function update(PredioRequest $request, Predio $predio)
    {
        Gate::authorize('admin');
        $predio->update($request->validated() + ['user_id' => auth()->id()]);
        session()->flash('alert-success', 'Prédio atualizado com sucesso!');
        return redirect('/predios');
    }

    public function destroy(Predio $predio)
    {
        Gate::authorize('admin');

        foreach($predio->plantas as $planta) {
            session()->flash('alert-danger', 'Prédio não deletado, pois possui plantas cadastradas!');
            return back();
        }

        if($predio->salas->isEmpty() && $predio->racks->isEmpty()) {
            $predio->delete();
            session()->flash('alert-success', 'Prédio removido com sucesso!');
        } else {
            session()->flash('alert-danger', 'Prédio não deletado, pois possui salas ou racks cadastrados!');
        }
        return redirect('/predios');
    }
}
