<?php

namespace App\Http\Controllers;

use App\Models\PermissaoAcao;
use Illuminate\Http\Request;

class PermissaoAcaoController extends Controller
{
    public function index()
    {
        return response()->json(PermissaoAcao::orderBy('id_acao')->get());
    }

    public function show($id)
    {
        $acao = PermissaoAcao::findOrFail($id);
        return response()->json($acao);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome_acao' => 'required|string|max:50',
            'codigo_acao' => 'required|string|max:30|unique:tb_permissoes_acoes,codigo_acao',
            'descricao' => 'nullable|string|max:255',
        ]);

        $acao = PermissaoAcao::create($data);

        return response()->json($acao, 201);
    }

    public function update(Request $request, $id)
    {
        $acao = PermissaoAcao::findOrFail($id);

        $data = $request->validate([
            'nome_acao' => 'sometimes|string|max:50',
            'codigo_acao' => 'sometimes|string|max:30|unique:tb_permissoes_acoes,codigo_acao,' . $id . ',id_acao',
            'descricao' => 'nullable|string|max:255',
        ]);

        $acao->update($data);

        return response()->json($acao);
    }

    public function destroy($id)
    {
        $acao = PermissaoAcao::findOrFail($id);
        $acao->delete();

        return response()->json(null, 204);
    }
}
