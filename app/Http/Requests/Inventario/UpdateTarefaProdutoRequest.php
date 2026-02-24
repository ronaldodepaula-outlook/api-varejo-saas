<?php

namespace App\Http\Requests\Inventario;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTarefaProdutoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'quantidade_contada' => 'required|numeric|min:0',
            'observacao' => 'nullable|string|max:255',
        ];
    }
}
