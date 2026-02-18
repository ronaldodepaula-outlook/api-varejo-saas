<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Secao;

class SecaoController extends Controller
{
    // Listar seções por id_empresa e id_categoria
    public function secoesPorEmpresaCategoria($id_empresa, $id_categoria)
    {
        $secoes = Secao::with('categoria')
            ->where('id_empresa', $id_empresa)
            ->where('id_categoria', $id_categoria)
            ->orderBy('nome_secao')
            ->get();
        return response()->json($secoes);
    }
    // Listar com paginação
    public function index(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $secoes = Secao::with('categoria')
            ->daEmpresa($empresaId)
            ->orderBy('nome_secao')
            ->paginate(20);

        return response()->json($secoes);
    }

    // Listar todas as seções de uma empresa
    public function todosPorEmpresa(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $secoes = Secao::with('categoria')
            ->daEmpresa($empresaId)
            ->orderBy('nome_secao')
            ->get();

        return response()->json($secoes);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa' => 'required|integer|exists:tb_empresas,id_empresa',
            'id_categoria' => 'required|integer|exists:tb_categorias,id_categoria',
            'nome_secao' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        $secao = Secao::create($data);

        return response()->json(['success' => true, 'data' => $secao], 201);
    }

    public function show(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $secao = Secao::daEmpresa($empresaId)
            ->with('grupos')
            ->findOrFail($id);

        return response()->json($secao);
    }

    public function update(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $secao = Secao::daEmpresa($empresaId)->findOrFail($id);

        $data = $request->validate([
            'id_categoria' => 'required|integer|exists:tb_categorias,id_categoria',
            'nome_secao' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        $secao->update($data);

        return response()->json(['success' => true, 'data' => $secao]);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $secao = Secao::daEmpresa($empresaId)->findOrFail($id);
        $secao->delete();

        return response()->json(['success' => true]);
    }

    // Listar seções de uma categoria específica
    public function porCategoria(Request $request, $idCategoria)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $secoes = Secao::daEmpresa($empresaId)
            ->where('id_categoria', $idCategoria)
            ->orderBy('nome_secao')
            ->get();

        return response()->json($secoes);
    }
}
