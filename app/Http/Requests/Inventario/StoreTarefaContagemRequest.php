<?php

namespace App\Http\Requests\Inventario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTarefaContagemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $idCapa = $this->input('id_capa_inventario');
        return [
            'id_capa_inventario' => 'required|integer|exists:tb_capa_inventario,id_capa_inventario',
            'id_usuario' => 'required|integer|exists:tb_usuarios,id_usuario',
            'id_supervisor' => 'nullable|integer|exists:tb_usuarios,id_usuario',
            'tipo_tarefa' => 'required|in:contagem_inicial,recontagem,conferencia',
            'observacoes' => 'nullable|string',
            'produtos' => 'nullable|array',
            'produtos.*' => [
                'integer',
                Rule::exists('tb_inventario', 'id_produto')->where(function ($query) use ($idCapa) {
                    if ($idCapa) {
                        $query->where('id_capa_inventario', $idCapa);
                    }
                }),
            ],
        ];
    }
}
