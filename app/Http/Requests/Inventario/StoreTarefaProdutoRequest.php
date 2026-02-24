<?php

namespace App\Http\Requests\Inventario;

use Illuminate\Foundation\Http\FormRequest;

class StoreTarefaProdutoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'produtos' => 'required|array|min:1',
            'produtos.*' => 'integer',
        ];
    }
}
