<?php

namespace App\Http\Controllers;

use App\Models\ModuloSistema;
use Illuminate\Http\Request;

class ModuloSistemaController extends Controller
{
    public function index(Request $request)
    {
        $query = ModuloSistema::query();

        if ($request->has('ativo')) {
            $query->where('ativo', (int) $request->get('ativo') === 1);
        }

        if ($request->has('id_modulo_pai')) {
            $query->where('id_modulo_pai', $request->get('id_modulo_pai'));
        }

        $modulos = $query->orderBy('ordem')->orderBy('nome_modulo')->get();

        return response()->json($modulos);
    }

    public function arvore(Request $request)
    {
        $query = ModuloSistema::query();
        if (!$request->boolean('incluir_inativos')) {
            $query->where('ativo', true);
        }

        $modulos = $query->orderBy('ordem')->orderBy('nome_modulo')->get();

        $porPai = $modulos->groupBy('id_modulo_pai');

        $montar = function ($paiId) use (&$montar, $porPai) {
            $filhos = $porPai->get($paiId, collect());
            return $filhos->map(function ($item) use ($montar) {
                $dados = $item->toArray();
                $dados['submodulos'] = $montar($item->id_modulo);
                return $dados;
            })->values();
        };

        return response()->json($montar(null));
    }

    public function show($id)
    {
        $modulo = ModuloSistema::findOrFail($id);
        return response()->json($modulo);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_modulo_pai' => 'nullable|exists:tb_modulos_sistema,id_modulo',
            'nome_modulo' => 'required|string|max:100',
            'descricao' => 'nullable|string|max:255',
            'icone' => 'nullable|string|max:50',
            'rota' => 'nullable|string|max:255',
            'ordem' => 'nullable|integer',
            'ativo' => 'nullable|boolean',
        ]);

        $modulo = ModuloSistema::create($data);

        return response()->json($modulo, 201);
    }

    public function update(Request $request, $id)
    {
        $modulo = ModuloSistema::findOrFail($id);

        $data = $request->validate([
            'id_modulo_pai' => 'nullable|exists:tb_modulos_sistema,id_modulo',
            'nome_modulo' => 'sometimes|string|max:100',
            'descricao' => 'nullable|string|max:255',
            'icone' => 'nullable|string|max:50',
            'rota' => 'nullable|string|max:255',
            'ordem' => 'nullable|integer',
            'ativo' => 'nullable|boolean',
        ]);

        $modulo->update($data);

        return response()->json($modulo);
    }

    public function destroy($id)
    {
        $modulo = ModuloSistema::findOrFail($id);
        $modulo->delete();

        return response()->json(null, 204);
    }
}
