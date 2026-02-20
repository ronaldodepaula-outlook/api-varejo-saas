<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CotacaoCabecalho;
use App\Models\CotacaoItem;
use App\Models\CotacaoFornecedor;
use App\Models\CotacaoResposta;

class CotacaoController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $query = CotacaoCabecalho::query();
        if ($empresaId) $query->daEmpresa($empresaId);

        $cotacoes = $query->orderBy('data_cotacao', 'desc')->paginate(20);
        return response()->json($cotacoes);
    }

    public function todosPorEmpresa(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');
        $cotacoes = CotacaoCabecalho::daEmpresa($empresaId)->orderBy('data_cotacao','desc')->get();
        return response()->json($cotacoes);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa'=>'required|integer|exists:tb_empresas,id_empresa',
            'id_filial'=>'required|integer|exists:tb_filiais,id_filial',
            'descricao'=>'nullable|string|max:255',
            'data_cotacao'=>'required|date',
            'data_validade'=>'nullable|date',
            'observacoes'=>'nullable|string',
        ]);

        $data['id_usuario_criador'] = auth()->user()->id_usuario ?? $request->get('id_usuario_criador');

        $cotacao = CotacaoCabecalho::create($data);
        return response()->json(['success'=>true,'data'=>$cotacao],201);
    }

    public function show(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');
        $cotacao = CotacaoCabecalho::daEmpresa($empresaId)->with(['itens.produto','fornecedores.fornecedor','fornecedores.respostas.produto'])->findOrFail($id);
        return response()->json($cotacao);
    }

    public function update(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');
        $cotacao = CotacaoCabecalho::daEmpresa($empresaId)->findOrFail($id);

        $data = $request->validate([
            'descricao'=>'nullable|string|max:255',
            'data_validade'=>'nullable|date',
            'status'=>'nullable|in:aberta,enviada,parcial,concluida,cancelada',
            'observacoes'=>'nullable|string',
        ]);

        $cotacao->update($data);
        return response()->json(['success'=>true,'data'=>$cotacao]);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');
        $cotacao = CotacaoCabecalho::daEmpresa($empresaId)->findOrFail($id);
        $cotacao->delete();
        return response()->json(['success'=>true]);
    }

    // Items
    public function storeItem(Request $request, $id_cotacao)
    {
        $cotacao = CotacaoCabecalho::findOrFail($id_cotacao);
        $data = $request->validate([
            'id_produto'=>'required|integer|exists:tb_produtos,id_produto',
            'quantidade'=>'required|numeric',
            'unidade_medida'=>'nullable|string|max:10',
            'observacao'=>'nullable|string|max:255'
        ]);

        $data['id_cotacao'] = $id_cotacao;
        $item = CotacaoItem::create($data);
        return response()->json(['success'=>true,'data'=>$item],201);
    }

    public function updateItem(Request $request, $id_cotacao, $id_item)
    {
        $item = CotacaoItem::where('id_cotacao',$id_cotacao)->findOrFail($id_item);
        $data = $request->validate([
            'quantidade'=>'nullable|numeric',
            'unidade_medida'=>'nullable|string|max:10',
            'observacao'=>'nullable|string|max:255'
        ]);
        $item->update($data);
        return response()->json(['success'=>true,'data'=>$item]);
    }

    public function destroyItem($id_cotacao, $id_item)
    {
        $item = CotacaoItem::where('id_cotacao',$id_cotacao)->findOrFail($id_item);
        $item->delete();
        return response()->json(['success'=>true]);
    }

    // Fornecedores e respostas crud minimal
    public function addFornecedor(Request $request, $id_cotacao)
    {
        $data = $request->validate([
            'id_fornecedor'=>'required|integer|exists:tb_fornecedores,id_fornecedor'
        ]);

        $data['id_cotacao'] = $id_cotacao;
        $cf = CotacaoFornecedor::create($data);
        return response()->json(['success'=>true,'data'=>$cf],201);
    }

    public function storeResposta(Request $request, $id_cotacao_fornecedor)
    {
        $data = $request->validate([
            'id_produto'=>'required|integer|exists:tb_produtos,id_produto',
            'quantidade'=>'required|numeric',
            'preco_unitario'=>'required|numeric',
            'prazo_entrega_item'=>'nullable|integer',
            'observacao'=>'nullable|string|max:255',
            'selecionado'=>'nullable|boolean'
        ]);

        $data['id_cotacao_fornecedor'] = $id_cotacao_fornecedor;
        $resp = CotacaoResposta::create($data);
        return response()->json(['success'=>true,'data'=>$resp],201);
    }
}
