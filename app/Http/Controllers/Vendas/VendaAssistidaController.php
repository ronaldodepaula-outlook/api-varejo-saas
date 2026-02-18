<?php
namespace App\Http\Controllers\Vendas;
use App\Http\Controllers\Controller;
use App\Models\VendaAssistida;
use App\Models\ItemVendaAssistida;
use App\Models\Estoque;
use App\Models\Movimentacao;
use App\Models\Filial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendaAssistidaController extends Controller {
    public function index(Request $r) {
        $empresa = $r->header('X-Empresa-Id');
        $q = VendaAssistida::with('cliente','itens');
        if ($empresa) $q->where('id_empresa',$empresa);
        return response()->json($q->paginate(25));
    }

    /**
     * Listar todas as vendas assistidas de uma empresa.
     * Suporta paginação via query string `per_page`.
     * Rota sugerida: GET /vendasAssistidas/assistidas/empresa/{id_empresa}
     */
    public function listarPorEmpresa(Request $request, $id_empresa)
    {
        $empresaId = (int) $id_empresa;

        // Verifica se a empresa existe
        $empresaExists = DB::table('tb_empresas')->where('id_empresa', $empresaId)->exists();
        if (! $empresaExists) {
            return response()->json(['message' => 'Empresa não encontrada'], 404);
        }

        // Ordenar pela data da venda (coluna existente na tabela: data_venda)
        $query = VendaAssistida::with('cliente', 'itens')
            ->where('id_empresa', $empresaId)
            ->orderBy('data_venda', 'desc');

        $perPage = $request->query('per_page');
        if ($perPage && is_numeric($perPage) && (int)$perPage > 0) {
            return response()->json($query->paginate((int) $perPage));
        }

        $vendas = $query->get();
        return response()->json($vendas);
    }

    public function store(Request $r) {
        $payload = $r->only(['id_empresa','id_filial','id_cliente','pseudonymous_customer_id','id_usuario','tipo_venda','forma_pagamento','valor_total','observacao']);
        DB::beginTransaction();
        try {
            $v = VendaAssistida::create($payload);
            if ($r->has('itens') && is_array($r->itens)) {
                foreach($r->itens as $it) {
                    // garantir que cada item tenha id_venda, id_empresa e id_filial antes de inserir
                    $it['id_venda'] = $v->id_venda;
                    $it['id_empresa'] = $v->id_empresa;
                    // id_filial pode ser nulo em alguns fluxos; use o valor da venda quando disponível
                    $it['id_filial'] = $v->id_filial ?? null;
                    ItemVendaAssistida::create($it);
                }
            }
            DB::commit();
            return response()->json($v->load('itens'),201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()],500);
        }
    }

    public function show($id) { return response()->json(VendaAssistida::with('cliente','itens')->findOrFail($id)); }

    public function update(Request $r, $id) {
        $v = VendaAssistida::findOrFail($id);
        $v->update($r->all());
        return response()->json($v);
    }

    // Finalizar venda (movimenta estoque e gera débito se necessário)
    public function finalizar(Request $r, $id) {
        $v = VendaAssistida::with('itens')->findOrFail($id);
        if ($v->status === 'finalizada') return response()->json(['message'=>'Já finalizada'],400);
        // Permite sobrescrever id_empresa / id_filial por headers (útil para testes ou clientes sem atualizar a venda em DB)
        $headerEmpresa = $r->header('X-Empresa-Id');
        $headerFilial = $r->header('X-Filial-Id');
        $id_empresa = $headerEmpresa ?? $v->id_empresa;
        $id_filial = $headerFilial ?? $v->id_filial;

        // Verifica se a filial está informada e existe para esta empresa
        if (empty($id_filial)) {
            return response()->json(['error' => "Filial não informada para esta venda. id_venda={$v->id_venda}, id_empresa={$id_empresa}"],400);
        }
        if (!Filial::where('id_filial',$id_filial)->where('id_empresa',$id_empresa)->exists()) {
            return response()->json(['error' => "Filial informada não existe para esta empresa. id_filial={$id_filial}, id_empresa={$id_empresa}"],400);
        }

        DB::beginTransaction();
        try {
            // movimenta estoque por item
            foreach($v->itens as $it) {
                // usar id_empresa/id_filial possivelmente sobrescritos pelos headers
                $e = Estoque::where('id_empresa',$id_empresa)->where('id_filial',$id_filial)->where('id_produto',$it->id_produto)->first();
                $saldo_anterior = $e ? $e->quantidade : 0;
                Estoque::adjustStock($id_empresa,$id_filial,$it->id_produto,-1 * $it->quantidade);
                $saldo_atual = ($e ? $e->quantidade : 0) - $it->quantidade;
                // normaliza a origem para garantir que seja um valor válido
                $origem = \App\Models\Movimentacao::normalizeOrigem('venda_assistida');
                Movimentacao::create([
                    'id_empresa' => $id_empresa,
                    'id_filial' => $id_filial,
                    'id_produto' => $it->id_produto,
                    'tipo_movimentacao' => 'saida',
                    'origem' => 'manual', // define diretamente como 'manual' para garantir compatibilidade
                    'id_referencia' => $v->id_venda,
                    'quantidade' => $it->quantidade,
                    'saldo_anterior' => $saldo_anterior,
                    'saldo_atual' => $saldo_atual,
                    'observacao' => 'Venda assistida ' . $v->id_venda,
                    'id_usuario' => $v->id_usuario,
                    'data_movimentacao' => now()
                ]);
            }
            // gerar debito automático se aplicável
            if ($v->forma_pagamento === 'fiado' || $v->tipo_venda === 'prevenda') {
                DB::table('tb_debitos_clientes')->insert([
                    'id_empresa' => $id_empresa,
                    'id_filial' => $id_filial,
                    'id_cliente' => $v->id_cliente,
                    'pseudonymous_customer_id' => $v->pseudonymous_customer_id,
                    'id_venda' => $v->id_venda,
                    'valor' => $v->valor_total,
                    'status' => 'pendente',
                    'data_geracao' => now()
                ]);
            }
            // Atualiza diretamente via DB para ter mais controle sobre o SQL gerado
            DB::table('tb_vendas_assistidas')
                ->where('id_venda', $v->id_venda)
                ->update([
                    'status' => DB::raw("'finalizada'"),
                    'data_fechamento' => now()
                ]);
            // Recarrega o modelo após a atualização
            $v->refresh();
            DB::commit();
            return response()->json(['finalizada'=>true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()],500);
        }
    }

    // Cancelar venda: devolver estoque (inserir como entrada - origem 'cancelamento')
    public function cancelar(Request $r, $id) {
        $v = VendaAssistida::with('itens')->findOrFail($id);
        DB::beginTransaction();
        try {
            foreach($v->itens as $it) {
                $e = Estoque::where('id_empresa',$v->id_empresa)->where('id_filial',$v->id_filial)->where('id_produto',$it->id_produto)->first();
                $saldo_anterior = $e ? $e->quantidade : 0;
                Estoque::adjustStock($v->id_empresa,$v->id_filial,$it->id_produto, $it->quantidade);
                $saldo_atual = ($e ? $e->quantidade : 0) + $it->quantidade;
                Movimentacao::create([
                    'id_empresa'=>$v->id_empresa,'id_filial'=>$v->id_filial,'id_produto'=>$it->id_produto,'tipo_movimentacao'=>'entrada',
                    'origem'=>\App\Models\Movimentacao::normalizeOrigem('cancelamento_venda_assistida'),'id_referencia'=>$v->id_venda,'quantidade'=>$it->quantidade,'saldo_anterior'=>$saldo_anterior,
                    'saldo_atual'=>$saldo_atual,'observacao'=>'Devolução por cancelamento venda '.$v->id_venda,'id_usuario'=>$v->id_usuario,'data_movimentacao'=>now()
                ]);
            }
            $v->status = 'cancelada';
            $v->save();
            DB::commit();
            return response()->json(['cancelada'=>true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()],500);
        }
    }
}