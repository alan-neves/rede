<?php

namespace App\Http\Controllers;

use App\Models\Predio;

class IndexController extends Controller
{
    public function index(){

        $predios = Predio::with([
            'racks' => fn ($q) => $q->orderBy('nome'),
            'racks.equipamentos' => fn ($q) => $q->orderBy('ordem'),
        ])->orderBy('nome')->get();

        return view('index',['predios' => $predios]);

    }
}
