<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoCompraCabecalho;
use App\Models\PedidoCompraItem;

class PedidoCompraController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');
        $query = PedidoCompraCabecalho::query();
        if ($empresaId) $query->where('id_empresa',$empresaId);
        return response()->json($query->orderBy('data_pedido','desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa'=>'required|integer|exists:tb_empresas,id_empresa',
            'id_filial'=>'required|integer|exists:tb_filiais,id_filial',
            'id_fornecedor'=>'required|integer|exists:tb_fornecedores,id_fornecedor',
            'id_cotacao'=>'nullable|integer|exists:tb_cotacoes_cabecalho,id_cotacao',
            'data_pedido'=>'required|date',
            'data_previsao_entrega'=>'nullable|date',
            'condicoes_pagamento'=>'nullable|string',
            'valor_total'=>'nullable|numeric',
            'status'=>'nullable|in:rascunho,enviado,parcial,concluido,cancelado',
            'observacoes'=>'nullable|string'
        ]);

        $data['id_usuario_criador'] = auth()->user()->id_usuario ?? null;
        $pedido = PedidoCompraCabecalho::create($data);
        return response()->json(['success'=>true,'data'=>$pedido],201);
    }

    public function show(Request $request,$id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');
        $pedido = PedidoCompraCabecalho::where('id_empresa',$empresaId)->with('itens.produto')->findOrFail($id);
        return response()->json($pedido);
    }

    public function update(Request $request,$id)
    {
        $pedido = PedidoCompraCabecalho::findOrFail($id);
        $data = $request->validate([
            'data_previsao_entrega'=>'nullable|date',
            'condicoes_pagamento'=>'nullable|string',
            'status'=>'nullable|in:rascunho,enviado,parcial,concluido,cancelado',
            'observacoes'=>'nullable|string'
        ]);
        $pedido->update($data);
        return response()->json(['success'=>true,'data'=>$pedido]);
    }

    public function destroy($id)
    {
        $pedido = PedidoCompraCabecalho::findOrFail($id);
        $pedido->delete();
        return response()->json(['success'=>true]);
    }

    // Itens
    public function storeItem(Request $request,$id_pedido)
    {
        $data = $request->validate([
            'id_produto'=>'required|integer|exists:tb_produtos,id_produto',
            'id_resposta_cotacao'=>'nullable|integer|exists:tb_cotacoes_respostas,id_resposta',
            'quantidade'=>'required|numeric',
            'preco_unitario'=>'required|numeric',
            'desconto'=>'nullable|numeric',
            'acrescimo'=>'nullable|numeric',
            'observacao'=>'nullable|string'
        ]);
        $data['id_pedido'] = $id_pedido;
        $item = PedidoCompraItem::create($data);
        return response()->json(['success'=>true,'data'=>$item],201);
    }

    public function updateItem(Request $request,$id_pedido,$id_item)
    {
        $item = PedidoCompraItem::where('id_pedido',$id_pedido)->findOrFail($id_item);
        $data = $request->validate([
            'quantidade'=>'nullable|numeric',
            'preco_unitario'=>'nullable|numeric',
            'desconto'=>'nullable|numeric',
            'acrescimo'=>'nullable|numeric',
            'observacao'=>'nullable|string'
        ]);
        $item->update($data);
        return response()->json(['success'=>true,'data'=>$item]);
    }

    public function destroyItem($id_pedido,$id_item)
    {
        $item = PedidoCompraItem::where('id_pedido',$id_pedido)->findOrFail($id_item);
        $item->delete();
        return response()->json(['success'=>true]);
    }
}
