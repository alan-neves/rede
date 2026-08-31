<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EquipamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $equipamentoId = $this->route('equipamento')?->id;

        $rules = [
            'hostname' => 'required|string|max:255|unique:equipamentos,hostname,' . $equipamentoId,
            'ip' => 'required|ip',
            'rack_id' => 'required|exists:racks,id',
            'modelo_switch_id' => 'required|exists:modelo_switches,id',
            'tipo' => 'required|in:A,W,C,V',
            'ordem' => 'nullable|integer|min:0',
            'comentario' => 'nullable|string',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'hostname.required' => 'O hostname é obrigatório',
            'hostname.unique' => 'Já existe um equipamento com este hostname',
            'ip.required' => 'O IP é obrigatório',
            'ip.ip' => 'Informe um IP válido',
            'rack_id.required' => 'Selecione um rack',
            'modelo_switch_id.required' => 'Selecione um modelo de switch',
            'tipo.required' => 'Selecione o tipo do equipamento',
            'tipo.in' => 'Tipo inválido',
        ];
    }
}
