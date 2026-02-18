<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subgrupo;

class SubgrupoController extends Controller
{
    // Listar subgrupos por id_empresa e id_grupo
    public function subgruposPorEmpresaGrupo($id_empresa, $id_grupo)
    {
        $subgrupos = Subgrupo::with('grupo')
            ->where('id_empresa', $id_empresa)
            ->where('id_grupo', $id_grupo)
            ->orderBy('nome_subgrupo')
            ->get();
        return response()->json($subgrupos);
    }
    public function index(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $subgrupos = Subgrupo::with('grupo')
            ->daEmpresa($empresaId)
            ->orderBy('nome_subgrupo')
            ->paginate(20);

        return response()->json($subgrupos);
    }

    public function todosPorEmpresa(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $subgrupos = Subgrupo::with('grupo')
            ->daEmpresa($empresaId)
            ->orderBy('nome_subgrupo')
            ->get();

        return response()->json($subgrupos);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa' => 'required|integer|exists:tb_empresas,id_empresa',
            'id_grupo' => 'required|integer|exists:tb_grupos,id_grupo',
            'nome_subgrupo' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        $subgrupo = Subgrupo::create($data);

        return response()->json(['success' => true, 'data' => $subgrupo], 201);
    }

    public function show(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $subgrupo = Subgrupo::daEmpresa($empresaId)->findOrFail($id);

        return response()->json($subgrupo);
    }

    public function update(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $subgrupo = Subgrupo::daEmpresa($empresaId)->findOrFail($id);

        $data = $request->validate([
            'id_grupo' => 'required|integer|exists:tb_grupos,id_grupo',
            'nome_subgrupo' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        $subgrupo->update($data);

        return response()->json(['success' => true, 'data' => $subgrupo]);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $subgrupo = Subgrupo::daEmpresa($empresaId)->findOrFail($id);
        $subgrupo->delete();

        return response()->json(['success' => true]);
    }

    // Listar subgrupos de um grupo específico
    public function porGrupo(Request $request, $idGrupo)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $subgrupos = Subgrupo::daEmpresa($empresaId)
            ->where('id_grupo', $idGrupo)
            ->orderBy('nome_subgrupo')
            ->get();

        return response()->json($subgrupos);
    }
}
