<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipamento;

class EquipamentoController extends Controller
{
    public function show(Request $request, Equipamento $equipamento){
        $this->authorize('user');
        return view('equipamentos.show',[
           'equipamento' => $equipamento, 
        ]);
    }
}
