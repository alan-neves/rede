<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoPortaRequest extends FormRequest
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
            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tipo_portas')->ignore($this->route('tipoPorta'))
            ],
            'cor' => [
                'nullable',
                'string',
                'hex_color',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do tipo de porta é obrigatório',
            'nome.max' => 'O nome não pode ter mais que 255 caracteres',
            'nome.unique' => 'Já existe um tipo de porta com este nome',
        ];
    }
}
