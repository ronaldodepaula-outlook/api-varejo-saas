<?php
namespace App\Http\Controllers\Vendas;
use App\Http\Controllers\Controller;
use App\Models\ItemVendaAssistida;
use App\Models\Estoque;
use App\Models\Movimentacao;
use App\Models\VendaAssistida;
use App\Models\Filial;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemVendaAssistidaController extends Controller {
    public function index() { return ItemVendaAssistida::paginate(25); }
    public function store(Request $r) {
        // Try to obtain id_empresa / id_filial from header, payload or related venda
        $id_empresa = $r->header('X-Empresa-Id') ?? $r->input('id_empresa');
        $id_filial = $r->header('X-Filial-Id') ?? $r->input('id_filial');

        // If id_venda provided, use the venda values as fallback
        if ($r->filled('id_venda')) {
            $v = VendaAssistida::find($r->input('id_venda'));
            if ($v) {
                $id_empresa = $id_empresa ?? $v->id_empresa;
                $id_filial = $id_filial ?? $v->id_filial;
            }
        }

        // Final defaults (avoid NULL for DB columns that are NOT NULL)
        $id_empresa = $id_empresa ?? 1;

        // Para operações que afetam estoque, a filial deve ser informada (não usar default).
        if (empty($id_filial)) {
            return response()->json(['error' => 'Campo id_filial obrigatório para criação de itens que movimentam estoque.'],400);
        }

        // Valida se a filial pertence à empresa
        if (!Filial::where('id_filial',$id_filial)->where('id_empresa',$id_empresa)->exists()) {
            return response()->json(['error' => "Filial informada não existe para esta empresa. id_filial={$id_filial}, id_empresa={$id_empresa}"],400);
        }

        $data = $r->only(['id_venda','id_produto','quantidade','valor_unitario']);
    $data['id_empresa'] = (int)$id_empresa;
    $data['id_filial'] = (int)$id_filial;

        // Valida que o produto exista antes de criar o item
        if (empty($data['id_produto']) || !Produto::where('id_produto', $data['id_produto'])->exists()) {
            return response()->json(['error' => "Produto informado não existe. id_produto={$data['id_produto']}"],400);
        }

        $item = ItemVendaAssistida::create($data);
        return response()->json($item,201);
    }
    public function show($id) { return ItemVendaAssistida::findOrFail($id); }
    public function update(Request $r,$id) { $it = ItemVendaAssistida::findOrFail($id); $it->update($r->all()); return response()->json($it); }

    // Quando excluir um item: reentrar no estoque como 'entrada' (origem: cancelamento_item)
    public function destroy($id) {
        $it = ItemVendaAssistida::findOrFail($id);
        DB::beginTransaction();
        try {
            // Get parent venda for company/filial info
            $venda = $it->venda()->first();
            $id_empresa = $venda ? $venda->id_empresa : null;
            $id_filial = $venda ? $venda->id_filial : null;
            $e = Estoque::where('id_empresa',$id_empresa)->where('id_filial',$id_filial)->where('id_produto',$it->id_produto)->first();
            $saldo_anterior = $e ? $e->quantidade : 0;
            // add back to stock
            Estoque::adjustStock($id_empresa,$id_filial,$it->id_produto,$it->quantidade);
            $saldo_atual = ($e ? $e->quantidade : 0) + $it->quantidade;
            Movimentacao::create([
                'id_empresa'=>$id_empresa,'id_filial'=>$id_filial,'id_produto'=>$it->id_produto,'tipo_movimentacao'=>'entrada',
                'origem'=>\App\Models\Movimentacao::normalizeOrigem('cancelamento_item'),'id_referencia'=>$it->id_item_venda,'quantidade'=>$it->quantidade,'saldo_anterior'=>$saldo_anterior,
                'saldo_atual'=>$saldo_atual,'observacao'=>'Entrada por exclusao item venda assistida','id_usuario'=>null,'data_movimentacao'=>now()
            ]);
            $it->delete();
            DB::commit();
            return response()->json(['deleted'=>true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()],500);
        }
    }
}
