<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    // Listar todas as categorias de uma empresa pelo id_empresa
    public function categoriasPorEmpresa($id_empresa)
    {
        $categorias = Categoria::where('id_empresa', $id_empresa)
            ->orderBy('nome_categoria')
            ->get();
        return response()->json($categorias);
    }
    // Listar com paginação
    public function index(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $categorias = Categoria::daEmpresa($empresaId)
            ->orderBy('nome_categoria')
            ->paginate(20);

        return response()->json($categorias);
    }

    // Listar todas as categorias de uma empresa (sem paginação)
    public function todosPorEmpresa(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $categorias = Categoria::daEmpresa($empresaId)
            ->orderBy('nome_categoria')
            ->get();

        return response()->json($categorias);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa' => 'required|integer|exists:tb_empresas,id_empresa',
            'nome_categoria' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        $categoria = Categoria::create($data);

        return response()->json(['success' => true, 'data' => $categoria], 201);
    }

    public function show(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $categoria = Categoria::daEmpresa($empresaId)
            ->with('secoes')
            ->findOrFail($id);

        return response()->json($categoria);
    }

    public function update(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $categoria = Categoria::daEmpresa($empresaId)->findOrFail($id);

        $data = $request->validate([
            'nome_categoria' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        $categoria->update($data);

        return response()->json(['success' => true, 'data' => $categoria]);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $categoria = Categoria::daEmpresa($empresaId)->findOrFail($id);
        $categoria->delete();

        return response()->json(['success' => true]);
    }

    // Listar todas as seções de uma categoria
    public function secoes(Request $request, $idCategoria)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $secoes = Categoria::daEmpresa($empresaId)
            ->findOrFail($idCategoria)
            ->secoes()
            ->where('id_empresa', $empresaId)
            ->orderBy('nome_secao')
            ->get();

        return response()->json($secoes);
    }
}
