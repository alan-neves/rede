<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ModeloSwitchRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'fabricante' => 'required|string|max:255',
            'qtde_portas' => 'required|integer|min:1|max:96',
            'qtde_portas_poe' => 'nullable|integer|min:0|max:96',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do modelo é obrigatório',
            'fabricante.required' => 'O fabricante é obrigatório',
            'qtde_portas.required' => 'A quantidade de portas é obrigatória',
        ];
    }
}
