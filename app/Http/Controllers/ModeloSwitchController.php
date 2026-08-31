<?php

namespace App\Http\Controllers;

use App\Models\ModeloSwitch;
use App\Http\Requests\ModeloSwitchRequest;
use Illuminate\Support\Facades\Gate;

class ModeloSwitchController extends Controller
{
    public function index()
    {
        Gate::authorize('admin');
        return view('modelo-switches.index', [
            'modelos' => ModeloSwitch::orderBy('fabricante')->orderBy('nome')->get(),
        ]);
    }

    public function create()
    {
        Gate::authorize('admin');
        return view('modelo-switches.create');
    }

    public function store(ModeloSwitchRequest $request)
    {
        Gate::authorize('admin');
        ModeloSwitch::create($request->validated() + ['user_id' => auth()->id()]);
        session()->flash('alert-success', 'Modelo cadastrado com sucesso!');
        return redirect('/modelo-switches');
    }

    public function show(ModeloSwitch $modeloSwitch)
    {
        Gate::authorize('admin');
        return view('modelo-switches.show', ['modelo' => $modeloSwitch]);
    }

    public function edit(ModeloSwitch $modeloSwitch)
    {
        Gate::authorize('admin');
        return view('modelo-switches.edit', ['modelo' => $modeloSwitch]);
    }

    public function update(ModeloSwitchRequest $request, ModeloSwitch $modeloSwitch)
    {
        Gate::authorize('admin');
        $modeloSwitch->update($request->validated() + ['user_id' => auth()->id()]);
        session()->flash('alert-success', 'Modelo atualizado com sucesso!');
        return redirect('/modelo-switches');
    }

    public function destroy(ModeloSwitch $modeloSwitch)
    {
        Gate::authorize('admin');

        if ($modeloSwitch->equipamentos()->count() > 0) {
            session()->flash('alert-danger', 'Não é possível remover: existem equipamentos usando este modelo.');
            return back();
        }

        $modeloSwitch->delete();
        session()->flash('alert-success', 'Modelo removido com sucesso!');
        return redirect('/modelo-switches');
    }
}