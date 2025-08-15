<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'porta' => 'required|string|max:50',
            'tipo' => 'nullable|string|max:50',
            'equipamento_id' => 'required|exists:equipamentos,id'
        ];
    }

    public function messages(): array
    {
        return [
            'porta.required' => 'O número da porta é obrigatório',
            'porta.max' => 'O número da porta não pode exceder 50 caracteres',
            'tipo.max' => 'O tipo não pode exceder 50 caracteres',
            'equipamento_id.required' => 'O equipamento é obrigatório',
            'equipamento_id.exists' => 'O equipamento selecionado não existe'
        ];
    }
}