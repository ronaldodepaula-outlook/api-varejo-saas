<?php

namespace App\Http\Controllers\Api\Pdv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PdvCaixa;
use App\Models\PdvVenda;

class PdvCaixaController extends Controller
{
    // 🔹 Lista todos os caixas
    public function index()
    {
        return PdvCaixa::with('usuario')->orderBy('id_caixa', 'desc')->get();
    }

    // 🔹 Abre um novo caixa
    public function abrir(Request $request)
    {
        $caixa = PdvCaixa::create([
            'id_empresa' => $request->id_empresa,
            'id_filial' => $request->id_filial,
            'id_usuario' => $request->id_usuario,
            'valor_abertura' => $request->valor_abertura,
            'status' => 'aberto'
        ]);

        return response()->json(['message' => 'Caixa aberto com sucesso!', 'caixa' => $caixa], 201);
    }

    // 🔹 Fecha o caixa e executa a procedure de integração
    public function fechar($id)
{
    $caixa = PdvCaixa::findOrFail($id);

    if ($caixa->status === 'fechado') {
        return response()->json(['message' => 'Caixa já está fechado!'], 400);
    }

    // Atualiza o caixa no Laravel
    $caixa->update([
        'status' => 'fechado',
        'data_fechamento' => now(),
    ]);

    // Agora chama a procedure para processar estoque e movimentações
    \DB::statement("CALL sp_encerrar_caixa_pdv(?)", [$id]);

    return response()->json(['message' => 'Caixa fechado e estoque atualizado com sucesso!']);
}

    // 🔹 Mostra detalhes de um caixa específico
    public function show($id)
    {
        return PdvCaixa::with('vendas.itens', 'vendas.pagamentos')->findOrFail($id);
    }

    // 🔹 Lista caixas por empresa com filtro opcional de status
    public function porEmpresaStatus($id_empresa, Request $request)
    {
        $query = PdvCaixa::with('usuario', 'vendas')
            ->where('id_empresa', $id_empresa);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('id_filial')) {
            $query->where('id_filial', $request->id_filial);
        }

        $caixas = $query->orderBy('id_caixa', 'desc')->get();

        return response()->json($caixas);
    }

    // 🔹 Lista caixas por status e filial (query params)
    // Exemplo: /api/pdv/caixas/status?status=aberto&id_filial=1
    public function status(Request $request)
    {
        $query = PdvCaixa::with('usuario', 'vendas');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('id_filial')) {
            $query->where('id_filial', $request->id_filial);
        }

        $caixas = $query->orderBy('id_caixa', 'desc')->get();

        return response()->json($caixas);
    }

    // 🔹 Lista caixas e, quando id_caixa informado, traz os detalhes dos itens de venda com produto
    // Exemplo: /api/pdv/caixas/detalhes?id_caixa=123
    public function detalhes(Request $request)
    {
        $query = PdvCaixa::with(['vendas' => function ($q) {
            $q->with(['itens' => function ($qi) {
                $qi->with('produto');
            }]);
        }]);

        if ($request->filled('id_caixa')) {
            $caixa = $query->where('id_caixa', $request->id_caixa)->first();

            if (!$caixa) {
                return response()->json(['message' => 'Caixa não encontrado'], 404);
            }

            // Calcular totalizadores por venda
            $caixa->vendas = $caixa->vendas->map(function ($v) {
                $totalItens = 0;
                $somaItens = 0.0;
                foreach ($v->itens as $it) {
                    $totalItens += (int) $it->quantidade;
                    $somaItens += ((float) $it->quantidade * (float) $it->preco_unitario);
                }

                $v->total_itens = $totalItens;
                $v->soma_itens = number_format($somaItens, 2, '.', '');
                return $v;
            });

            return response()->json($caixa);
        }

        // Se não passar id_caixa, listar caixas simples
        $caixas = $query->orderBy('id_caixa', 'desc')->get();
        return response()->json($caixas);
    }

    // 🔹 Resumo diário de PDV por empresa (opcional por filial)
    public function resumoDia($id_empresa, Request $request)
    {
        $request->validate([
            'data' => 'required|date',
            'id_filial' => 'sometimes|integer'
        ]);

        $date = $request->data;

        // Buscar vendas do dia
        $vendasQuery = PdvVenda::where('id_empresa', $id_empresa)
            ->whereDate('data_venda', $date);

        if ($request->filled('id_filial')) {
            $vendasQuery->where('id_filial', $request->id_filial);
        }

        $vendas = $vendasQuery->with('pagamentos')->get();

        $totalVendas = $vendas->count();
        $somaValorTotal = $vendas->sum('valor_total');

        $somaPagamentos = 0;
        $somaTroco = 0;
        $porForma = [];

        foreach ($vendas as $v) {
            foreach ($v->pagamentos as $p) {
                $somaPagamentos += (float) $p->valor_pago;
                $somaTroco += isset($p->valor_troco) ? (float) $p->valor_troco : 0;
                $forma = $p->forma_pagamento ?? 'desconhecida';
                if (!isset($porForma[$forma])) $porForma[$forma] = 0;
                $porForma[$forma] += (float) $p->valor_pago;
            }
        }

        return response()->json([
            'data' => $date,
            'id_empresa' => (int) $id_empresa,
            'id_filial' => $request->id_filial ?? null,
            'total_vendas' => $totalVendas,
            'soma_valor_total' => number_format($somaValorTotal, 2, '.', ''),
            'soma_pagamentos' => number_format($somaPagamentos, 2, '.', ''),
            'soma_troco' => number_format($somaTroco, 2, '.', ''),
            'pagamentos_por_forma' => $porForma
        ]);
    }

    // 🔹 Abre caixa vinculando-o à empresa da URL
    public function aberturaPorEmpresa($id_empresa, Request $request)
    {
        $request->validate([
            'id_filial' => 'required|exists:tb_filiais,id_filial',
            'id_usuario' => 'required|exists:tb_usuarios,id_usuario',
            'valor_abertura' => 'required|numeric'
        ]);

        $caixa = PdvCaixa::create([
            'id_empresa' => $id_empresa,
            'id_filial' => $request->id_filial,
            'id_usuario' => $request->id_usuario,
            'valor_abertura' => $request->valor_abertura,
            'status' => 'aberto'
        ]);

        return response()->json(['message' => 'Caixa aberto com sucesso!', 'caixa' => $caixa], 201);
    }
}
