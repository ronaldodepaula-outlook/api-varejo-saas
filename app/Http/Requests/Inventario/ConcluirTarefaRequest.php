<?php

namespace App\Http\Requests\Inventario;

use Illuminate\Foundation\Http\FormRequest;

class ConcluirTarefaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'observacoes' => 'required|string|max:255',
            'forcar_conclusao' => 'sometimes|boolean',
        ];
    }
}
