<?php

namespace App\Http\Controllers;

use App\Models\Movimentacao;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MovimentacaoController extends Controller
{
    public function index()
    {
        $movimentacoes = Movimentacao::with(['empresa', 'filial', 'produto', 'usuario'])->get();
        return response()->json($movimentacoes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_empresa' => 'required|exists:tb_empresas,id_empresa',
            'id_filial' => 'required|exists:tb_filiais,id_filial',
            'id_produto' => 'required|exists:tb_produtos,id_produto',
            'tipo_movimentacao' => 'required|in:entrada,saida,transferencia,ajuste',
            'origem' => 'required|in:nota_fiscal,manual,transferencia,inventario,venda_assistida,cancelamento_venda_assistida,cancelamento_item,entrada_compra,Ordem_de_Producao',
            'id_referencia' => 'nullable|integer',
            'quantidade' => 'required|numeric',
            'saldo_anterior' => 'required|numeric',
            'saldo_atual' => 'required|numeric',
            'custo_unitario' => 'nullable|numeric',
            'observacao' => 'nullable|string|max:255',
            'id_usuario' => 'required|exists:tb_usuarios,id_usuario'
        ]);

        $movimentacao = Movimentacao::create($request->all());
        return response()->json($movimentacao, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $movimentacao = Movimentacao::with(['empresa', 'filial', 'produto', 'usuario'])->findOrFail($id);
        return response()->json($movimentacao);
    }

    public function update(Request $request, $id)
    {
        $movimentacao = Movimentacao::findOrFail($id);
        
        $request->validate([
            'id_empresa' => 'sometimes|required|exists:tb_empresas,id_empresa',
            'id_filial' => 'sometimes|required|exists:tb_filiais,id_filial',
            'id_produto' => 'sometimes|required|exists:tb_produtos,id_produto',
            'tipo_movimentacao' => 'sometimes|required|in:entrada,saida,transferencia,ajuste',
            'origem' => 'sometimes|required|in:nota_fiscal,manual,transferencia,inventario,venda_assistida,cancelamento_venda_assistida,cancelamento_item,entrada_compra,Ordem_de_Producao',
            'id_referencia' => 'nullable|integer',
            'quantidade' => 'sometimes|required|numeric',
            'saldo_anterior' => 'sometimes|required|numeric',
            'saldo_atual' => 'sometimes|required|numeric',
            'custo_unitario' => 'nullable|numeric',
            'observacao' => 'nullable|string|max:255',
            'id_usuario' => 'sometimes|required|exists:tb_usuarios,id_usuario'
        ]);

        $movimentacao->update($request->all());
        return response()->json($movimentacao);
    }

    public function destroy($id)
    {
        $movimentacao = Movimentacao::findOrFail($id);
        $movimentacao->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    // Ficha de estoque: listar movimentações por produto, empresa e filial (ordem decrescente por data)
    public function fichaEstoquePorProdutoEmpresaFilial($id_empresa, $id_filial, $id_produto)
    {
        $movs = Movimentacao::with(['usuario'])
            ->where('id_empresa', $id_empresa)
            ->where('id_filial', $id_filial)
            ->where('id_produto', $id_produto)
            ->orderBy('data_movimentacao', 'desc')
            ->get();

        return response()->json($movs);
    }
}
