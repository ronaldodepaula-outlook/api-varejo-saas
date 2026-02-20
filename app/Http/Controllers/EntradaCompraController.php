<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntradaCompraCabecalho;
use App\Models\EntradaCompraItem;
use App\Models\EntradaCompraHistorico;
use Illuminate\Support\Facades\DB;

class EntradaCompraController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');
        $query = EntradaCompraCabecalho::query();
        if ($empresaId) $query->where('id_empresa',$empresaId);
        return response()->json($query->orderBy('data_entrada','desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa'=>'required|integer|exists:tb_empresas,id_empresa',
            'id_filial'=>'required|integer|exists:tb_filiais,id_filial',
            'id_fornecedor'=>'required|integer|exists:tb_fornecedores,id_fornecedor',
            'id_pedido'=>'nullable|integer|exists:tb_pedidos_compra_cabecalho,id_pedido',
            'id_nfe'=>'nullable|integer|exists:tb_nfe_cabecalho,id_nfe',
            'data_entrada'=>'required|date_format:Y-m-d H:i:s',
            'data_recebimento'=>'required|date_format:Y-m-d H:i:s',
            'tipo_entrada'=>'nullable|in:pedido,avulsa,devolucao',
            'valor_total'=>'nullable|numeric',
            'observacoes'=>'nullable|string'
        ]);

        $data['numero_entrada'] = $request->get('numero_entrada');
        $entrada = EntradaCompraCabecalho::create($data);
        return response()->json(['success'=>true,'data'=>$entrada],201);
    }

    public function show(Request $request,$id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');
        $entrada = EntradaCompraCabecalho::where('id_empresa',$empresaId)->with('itens.produto')->findOrFail($id);
        return response()->json($entrada);
    }

    public function update(Request $request,$id)
    {
        $entrada = EntradaCompraCabecalho::findOrFail($id);
        $data = $request->validate([
            'data_recebimento'=>'nullable|date_format:Y-m-d H:i:s',
            'status'=>'nullable|in:rascunho,recebido,conferido,cancelado',
            'observacoes'=>'nullable|string'
        ]);
        $entrada->update($data);
        return response()->json(['success'=>true,'data'=>$entrada]);
    }

    public function destroy($id)
    {
        $entrada = EntradaCompraCabecalho::findOrFail($id);
        $entrada->delete();
        return response()->json(['success'=>true]);
    }

    // Itens
    public function storeItem(Request $request,$id_entrada)
    {
        $data = $request->validate([
            'id_produto'=>'required|integer|exists:tb_produtos,id_produto',
            'id_item_pedido'=>'nullable|integer|exists:tb_pedidos_compra_itens,id_item_pedido',
            'quantidade_recebida'=>'required|numeric',
            'preco_unitario'=>'required|numeric',
            'lote'=>'nullable|string',
            'data_fabricacao'=>'nullable|date',
            'data_validade'=>'nullable|date',
            'observacao'=>'nullable|string'
        ]);
        $data['id_entrada'] = $id_entrada;
        $item = EntradaCompraItem::create($data);

        // create historico
        EntradaCompraHistorico::create(['id_entrada'=>$id_entrada,'acao'=>'criacao','descricao'=>'Item adicionado','id_usuario'=>auth()->user()->id_usuario ?? null]);

        // Optionally trigger stock movement via existing tb_movimentacoes logic elsewhere

        return response()->json(['success'=>true,'data'=>$item],201);
    }

    public function storeHistorico(Request $request,$id_entrada)
    {
        $data = $request->validate([
            'acao'=>'required|in:criacao,conferencia,cancelamento,estorno',
            'descricao'=>'nullable|string',
            'id_usuario'=>'nullable|integer|exists:tb_usuarios,id_usuario'
        ]);
        $data['id_entrada'] = $id_entrada;
        $hist = EntradaCompraHistorico::create($data + ['data_acao'=>now()]);
        return response()->json(['success'=>true,'data'=>$hist],201);
    }
}
