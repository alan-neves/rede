<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VincularPortaPatchPanelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sala_id' => 'required|exists:salas,id',
            'porta' => 'required|integer|min:1',
            'tipo_porta_id' => 'nullable|exists:tipo_portas,id',
            'tamanho' => 'nullable|numeric|min:0',
            'comentario' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'sala_id.required' => 'A sala é obrigatória',
            'sala_id.exists' => 'Sala inválida',
            'porta.required' => 'O número da porta é obrigatório',
            'porta.integer' => 'O número da porta deve ser um número inteiro',
            'porta.min' => 'O número da porta deve ser pelo menos 1',
            'tipo_porta_id.exists' => 'Tipo de porta inválido',
        ];
    }
}