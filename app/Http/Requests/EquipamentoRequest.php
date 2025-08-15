<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EquipamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'hostname' => 'required|unique:equipamentos',
            'model' => 'required|in:' . implode(',', \App\Models\Equipamento::model),
            'ip' => 'required|ip',
            'poe_type' => 'required|in:poe,poe+,none',
            'qtde_portas' => 'required|integer|min:1',
            'patrimonio' => 'nullable|string|max:255',
            'predio_id' => 'nullable|exists:predios,id',
            'rack_id' => 'nullable|exists:racks,id'
        ];

        if ($this->method() === 'PUT') {
            $rules['hostname'] = 'required|unique:equipamentos,hostname,' . $this->equipamento->id;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'hostname.required' => 'O hostname é obrigatório',
            'hostname.unique' => 'Este hostname já está em uso',
            'model.required' => 'O modelo é obrigatório',
            'model.in' => 'Modelo selecionado é inválido',
            'ip.required' => 'O IP é obrigatório',
            'ip.ip' => 'Informe um IP válido',
            'poe_type.required' => 'O tipo POE é obrigatório',
            'poe_type.in' => 'Tipo POE selecionado é inválido',
            'qtde_portas.required' => 'A quantidade de portas é obrigatória',
            'qtde_portas.integer' => 'A quantidade de portas deve ser um número inteiro',
            'qtde_portas.min' => 'O equipamento deve ter pelo menos 1 porta',
            'predio_id.exists' => 'O prédio selecionado não existe',
            'rack_id.exists' => 'O rack selecionado não existe'
        ];
    }
}