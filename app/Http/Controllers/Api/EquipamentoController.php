<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipamento;

class EquipamentoController extends Controller
{
    public function store(Request $request)
    {
        // Verificar autorização
        if($request->header('Authorization') != env('AUTHORIZATION_KEY')){
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Validação dos campos
        $validated = $request->validate([
            'hostname' => 'required|string|max:255',
            'ip' => 'required|ip',
            'rack_id' => 'required|exists:racks,id',
            'modelo_switch_id' => 'required|exists:modelo_switches,id',
            'tipo' => 'required|in:A,W,C,V',
            'comentario' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $validated['ordem'] = Equipamento::where('rack_id', $validated['rack_id'])->max('ordem') + 1;

        $equipamento = Equipamento::updateOrCreate(
            ['hostname' => $validated['hostname']],
            $validated
        );

        return response()->json([
            'message' => 'Equipamento criado/atualizado com sucesso',
            'equipamento' => $equipamento->load('modeloSwitch')
        ], 201);
    }
}
