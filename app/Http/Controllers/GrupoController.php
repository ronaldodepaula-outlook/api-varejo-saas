<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;

class GrupoController extends Controller
{
    // Listar grupos por id_empresa e id_secao
    public function gruposPorEmpresaSecao($id_empresa, $id_secao)
    {
        $grupos = Grupo::with('secao')
            ->where('id_empresa', $id_empresa)
            ->where('id_secao', $id_secao)
            ->orderBy('nome_grupo')
            ->get();
        return response()->json($grupos);
    }
    // Listar com paginação
    public function index(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $grupos = Grupo::with('secao')
            ->daEmpresa($empresaId)
            ->orderBy('nome_grupo')
            ->paginate(20);

        return response()->json($grupos);
    }

    // Listar todos os grupos da empresa
    public function todosPorEmpresa(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $grupos = Grupo::with('secao')
            ->daEmpresa($empresaId)
            ->orderBy('nome_grupo')
            ->get();

        return response()->json($grupos);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa' => 'required|integer|exists:tb_empresas,id_empresa',
            'id_secao' => 'required|integer|exists:tb_secoes,id_secao',
            'nome_grupo' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        $grupo = Grupo::create($data);

        return response()->json(['success' => true, 'data' => $grupo], 201);
    }

    public function show(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $grupo = Grupo::daEmpresa($empresaId)
            ->with('subgrupos')
            ->findOrFail($id);

        return response()->json($grupo);
    }

    public function update(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $grupo = Grupo::daEmpresa($empresaId)->findOrFail($id);

        $data = $request->validate([
            'id_secao' => 'required|integer|exists:tb_secoes,id_secao',
            'nome_grupo' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        $grupo->update($data);

        return response()->json(['success' => true, 'data' => $grupo]);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $grupo = Grupo::daEmpresa($empresaId)->findOrFail($id);
        $grupo->delete();

        return response()->json(['success' => true]);
    }

    // Listar grupos de uma seção específica
    public function porSecao(Request $request, $idSecao)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $grupos = Grupo::daEmpresa($empresaId)
            ->where('id_secao', $idSecao)
            ->orderBy('nome_grupo')
            ->get();

        return response()->json($grupos);
    }
}
