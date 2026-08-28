<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatchPanelRequest extends FormRequest
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
        $rules = [
            'nome' => 'required|string|max:255',
            'rack_id' => 'required|exists:racks,id',
            'qtde_portas' => 'required|integer|min:1',
            'visible' => 'nullable|boolean',
        ];
        
        return $rules;
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do patch panel é obrigatório',
            'nome.max' => 'O nome não pode ter mais que 255 caracteres',
            'rack_id.required' => 'O rack é obrigatório',
            'rack_id.exists' => 'O rack selecionado não existe',
            'qtde_portas.required' => 'A quantidade de portas é obrigatória',
            'qtde_portas.integer' => 'A quantidade de portas deve ser um número inteiro',
            'qtde_portas.min' => 'O patch panel deve ter pelo menos 1 porta',
        ];
    }
}
