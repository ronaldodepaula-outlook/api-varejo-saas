<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EstoqueController extends Controller
{
    public function index()
    {
        $estoques = Estoque::with(['empresa', 'filial', 'produto'])->get();
        return response()->json($estoques);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_empresa' => 'required|exists:tb_empresas,id_empresa',
            'id_filial' => 'required|exists:tb_filiais,id_filial',
            'id_produto' => 'required|exists:tb_produtos,id_produto',
            'quantidade' => 'required|numeric',
            'estoque_minimo' => 'nullable|numeric',
            'estoque_maximo' => 'nullable|numeric'
        ]);

        $estoque = Estoque::create($request->all());
        return response()->json($estoque, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $estoque = Estoque::with(['empresa', 'filial', 'produto'])->findOrFail($id);
        return response()->json($estoque);
    }

    public function update(Request $request, $id)
    {
        $estoque = Estoque::findOrFail($id);
        
        $request->validate([
            'id_empresa' => 'sometimes|required|exists:tb_empresas,id_empresa',
            'id_filial' => 'sometimes|required|exists:tb_filiais,id_filial',
            'id_produto' => 'sometimes|required|exists:tb_produtos,id_produto',
            'quantidade' => 'sometimes|required|numeric',
            'estoque_minimo' => 'nullable|numeric',
            'estoque_maximo' => 'nullable|numeric'
        ]);

        $estoque->update($request->all());
        return response()->json($estoque);
    }

    public function destroy($id)
    {
        $estoque = Estoque::findOrFail($id);
        $estoque->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function porFilialProduto($id_empresa, $id_filial, $id_produto)
    {
        $estoque = Estoque::with(['empresa', 'filial', 'produto'])
            ->where('id_empresa', $id_empresa)
            ->where('id_filial', $id_filial)
            ->where('id_produto', $id_produto)
            ->firstOrFail();

        return response()->json($estoque);
    }

    // Listar filiais que possuem estoque do produto em uma empresa
    public function filiaisComEstoquePorEmpresaProduto($id_empresa, $id_produto)
    {
        $registros = Estoque::with(['filial', 'produto'])
            ->where('id_empresa', $id_empresa)
            ->where('id_produto', $id_produto)
            ->where('quantidade', '>', 0)
            ->get();

        // Agrupa por filial e monta o payload com filial + detalhes do produto (incluindo quantidade/estoque)
        $resultado = $registros->groupBy('id_filial')->map(function ($itensPorFilial) {
            $estoque = $itensPorFilial->first();
            $filial = $estoque->filial;
            $produto = $estoque->produto ? $estoque->produto->toArray() : [];

            // Acrescenta dados específicos de estoque ao objeto produto
            $produto['quantidade'] = $estoque->quantidade;
            $produto['estoque_minimo'] = $estoque->estoque_minimo;
            $produto['estoque_maximo'] = $estoque->estoque_maximo;

            return [
                'filial' => $filial,
                'produto' => $produto,
            ];
        })->values();

        return response()->json($resultado);
    }
}