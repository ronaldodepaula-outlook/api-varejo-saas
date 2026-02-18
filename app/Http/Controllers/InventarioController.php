<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InventarioController extends Controller
{
    public function index()
    {
        $inventarios = Inventario::with(['capaInventario', 'empresa', 'filial', 'produto', 'usuario'])->get();
        return response()->json($inventarios);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_capa_inventario' => 'required|exists:tb_capa_inventario,id_capa_inventario',
            'id_empresa' => 'required|exists:tb_empresas,id_empresa',
            'id_filial' => 'required|exists:tb_filiais,id_filial',
            'id_produto' => 'required|exists:tb_produtos,id_produto',
            'quantidade_fisica' => 'required|numeric',
            'quantidade_sistema' => 'required|numeric',
            'motivo' => 'nullable|string|max:255',
            'id_usuario' => 'required|exists:tb_usuarios,id_usuario'
        ]);

        $inventario = Inventario::create($request->all());
        return response()->json($inventario, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $inventario = Inventario::with(['capaInventario', 'empresa', 'filial', 'produto', 'usuario'])->findOrFail($id);
        return response()->json($inventario);
    }

    public function update(Request $request, $id)
    {
        $inventario = Inventario::findOrFail($id);
        
        $request->validate([
            'id_capa_inventario' => 'sometimes|required|exists:tb_capa_inventario,id_capa_inventario',
            'id_empresa' => 'sometimes|required|exists:tb_empresas,id_empresa',
            'id_filial' => 'sometimes|required|exists:tb_filiais,id_filial',
            'id_produto' => 'sometimes|required|exists:tb_produtos,id_produto',
            'quantidade_fisica' => 'sometimes|required|numeric',
            'quantidade_sistema' => 'sometimes|required|numeric',
            'motivo' => 'nullable|string|max:255',
            'id_usuario' => 'sometimes|required|exists:tb_usuarios,id_usuario'
        ]);

        $inventario->update($request->all());
        return response()->json($inventario);
    }

    public function destroy($id)
    {
        $inventario = Inventario::findOrFail($id);
        $inventario->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    // Listar todos os itens de um id_capa_inventario
    public function listarPorCapa($id_capa_inventario)
    {
        $itens = Inventario::with(['produto', 'usuario', 'filial'])
            ->where('id_capa_inventario', $id_capa_inventario)
            ->orderBy('id_inventario', 'asc')
            ->get();

        return response()->json($itens);
    }
}