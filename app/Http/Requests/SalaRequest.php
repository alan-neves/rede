<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalaRequest extends FormRequest
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
        return [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|max:255',
            'predio_id' => 'required|exists:predios,id'
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome da sala é obrigatório',
            'nome.max' => 'O nome não pode ter mais que 255 caracteres',
            'descricao.max' => 'A descrição não pode ter mais que 255 caracteres',
            'predio_id.required' => 'O prédio é obrigatório',
        ];
    }
}
