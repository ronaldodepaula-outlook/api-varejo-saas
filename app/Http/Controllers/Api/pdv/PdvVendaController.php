<?php

namespace App\Http\Controllers\Api\Pdv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PdvVenda;
use App\Models\PdvItemVenda;
use App\Models\PdvPagamento;
use Illuminate\Support\Facades\DB;

class PdvVendaController extends Controller
{
    // 🔹 Registra uma nova venda
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $venda = PdvVenda::create([
                'id_caixa' => $request->id_caixa,
                'id_empresa' => $request->id_empresa,
                'id_filial' => $request->id_filial,
                'valor_total' => $request->valor_total,
                'tipo_venda' => $request->tipo_venda ?? 'venda',
                'status' => 'fechada'
            ]);

            foreach ($request->itens as $item) {
                PdvItemVenda::create([
                    'id_venda' => $venda->id_venda,
                    'id_produto' => $item['id_produto'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco_unitario']
                ]);
            }

            foreach ($request->pagamentos as $pg) {
                PdvPagamento::create([
                    'id_venda' => $venda->id_venda,
                    'forma_pagamento' => $pg['forma_pagamento'],
                    'valor_pago' => $pg['valor_pago'],
                    // valor_troco pode não ser informado pelo cliente; usar 0.00 por padrão
                    'valor_troco' => isset($pg['valor_troco']) ? $pg['valor_troco'] : 0.00
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Venda registrada com sucesso!', 'venda' => $venda], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erro ao registrar venda', 'error' => $e->getMessage()], 500);
        }
    }

    // 🔹 Lista vendas de um caixa
    public function porCaixa($id_caixa)
    {
        return PdvVenda::with('itens', 'pagamentos')->where('id_caixa', $id_caixa)->get();
    }

    // 🔹 Lista vendas por empresa e data (opcional por filial)
    public function vendasPorEmpresaData($id_empresa, Request $request)
    {
        $request->validate([
            'data' => 'required|date',
            'id_filial' => 'sometimes|integer'
        ]);

        $query = PdvVenda::with('itens', 'pagamentos')
            ->where('id_empresa', $id_empresa)
            ->whereDate('data_venda', $request->data);

        if ($request->filled('id_filial')) {
            $query->where('id_filial', $request->id_filial);
        }

        $vendas = $query->orderBy('id_venda', 'desc')->get();

        return response()->json($vendas);
    }

    // 🔹 Dashboard de vendas (casos de uso de mercado)
    // Retorna métricas agregadas por dia, por forma de pagamento e total por filial quando informado
    public function dashboard(Request $request)
    {
        $request->validate([
            'data_inicio' => 'sometimes|date',
            'data_fim' => 'sometimes|date',
            'id_empresa' => 'sometimes|integer',
            'id_filial' => 'sometimes|integer'
        ]);

        $query = PdvVenda::query();

        if ($request->filled('id_empresa')) {
            $query->where('id_empresa', $request->id_empresa);
        }

        if ($request->filled('id_filial')) {
            $query->where('id_filial', $request->id_filial);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_venda', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_venda', '<=', $request->data_fim);
        }

        $totalVendas = (clone $query)->count();
        $somaValorTotal = (clone $query)->sum('valor_total');

        // Pagamentos por forma (join pagamentos)
        $pagamentosPorForma = DB::table('tb_pdv_pagamentos as p')
            ->select('p.forma_pagamento', DB::raw('SUM(p.valor_pago) as total_pago'))
            ->join('tb_pdv_vendas as v', 'p.id_venda', '=', 'v.id_venda')
            ->when($request->filled('id_empresa'), function ($q) use ($request) {
                return $q->where('v.id_empresa', $request->id_empresa);
            })
            ->when($request->filled('id_filial'), function ($q) use ($request) {
                return $q->where('v.id_filial', $request->id_filial);
            })
            ->when($request->filled('data_inicio'), function ($q) use ($request) {
                return $q->whereDate('v.data_venda', '>=', $request->data_inicio);
            })
            ->when($request->filled('data_fim'), function ($q) use ($request) {
                return $q->whereDate('v.data_venda', '<=', $request->data_fim);
            })
            ->groupBy('p.forma_pagamento')
            ->get();

        // Vendas por dia
        $vendasPorDia = (clone $query)
            ->select(DB::raw('DATE(data_venda) as dia'), DB::raw('COUNT(*) as total_vendas'), DB::raw('SUM(valor_total) as soma_valor_total'))
            ->groupBy(DB::raw('DATE(data_venda)'))
            ->orderBy(DB::raw('DATE(data_venda)'), 'desc')
            ->get();

        return response()->json([
            'total_vendas' => $totalVendas,
            'soma_valor_total' => number_format($somaValorTotal, 2, '.', ''),
            'pagamentos_por_forma' => $pagamentosPorForma,
            'vendas_por_dia' => $vendasPorDia
        ]);
    }

    // 🔹 Lista vendas agrupadas por cupom (id_venda) com itens e totalizadores
    // Filtro: id_empresa, id_filial, data opcional
    public function vendasPorCupons(Request $request)
    {
        $request->validate([
            'id_empresa' => 'required|integer',
            'id_filial' => 'sometimes|integer',
            'data' => 'sometimes|date'
        ]);

        $query = PdvVenda::with(['itens'])
            ->where('id_empresa', $request->id_empresa);

        if ($request->filled('id_filial')) {
            $query->where('id_filial', $request->id_filial);
        }

        if ($request->filled('data')) {
            $query->whereDate('data_venda', $request->data);
        }

        $vendas = $query->orderBy('id_venda', 'desc')->get();

        // Mapear cada venda com seus totalizadores (soma itens)
        $result = $vendas->map(function ($v) {
            $totalItens = 0;
            $somaItens = 0.0;
            foreach ($v->itens as $it) {
                $totalItens += (int) $it->quantidade;
                $somaItens += ((float) $it->quantidade * (float) $it->preco_unitario);
            }

            return [
                'id_venda' => $v->id_venda,
                'id_caixa' => $v->id_caixa,
                'data_venda' => $v->data_venda,
                'valor_total' => number_format($v->valor_total, 2, '.', ''),
                'total_itens' => $totalItens,
                'soma_itens' => number_format($somaItens, 2, '.', ''),
                'itens' => $v->itens
            ];
        });

        return response()->json($result);
    }

    // 🔹 Lista vendas com itens e produto associados
    // Filtros: id_empresa (required), id_filial (optional), id_venda (optional), data (optional)
    public function vendasDetalhes(Request $request)
    {
        $request->validate([
            'id_empresa' => 'required|integer',
            'id_filial' => 'sometimes|integer',
            'id_venda' => 'sometimes|integer',
            'data' => 'sometimes|date'
        ]);

        $query = PdvVenda::with(['itens.produto', 'pagamentos'])
            ->where('id_empresa', $request->id_empresa);

        if ($request->filled('id_filial')) {
            $query->where('id_filial', $request->id_filial);
        }

        if ($request->filled('id_venda')) {
            $query->where('id_venda', $request->id_venda);
        }

        if ($request->filled('data')) {
            $query->whereDate('data_venda', $request->data);
        }

        $vendas = $query->orderBy('id_venda', 'desc')->get();

        $result = $vendas->map(function ($v) {
            $totalItens = 0;
            $somaItens = 0.0;
            foreach ($v->itens as $it) {
                $totalItens += (int) $it->quantidade;
                $somaItens += ((float) $it->quantidade * (float) $it->preco_unitario);
            }

            return [
                'id_venda' => $v->id_venda,
                'id_caixa' => $v->id_caixa,
                'id_empresa' => $v->id_empresa,
                'id_filial' => $v->id_filial,
                'data_venda' => $v->data_venda,
                'valor_total' => number_format($v->valor_total, 2, '.', ''),
                'total_itens' => $totalItens,
                'soma_itens' => number_format($somaItens, 2, '.', ''),
                'itens' => $v->itens,
                'pagamentos' => $v->pagamentos
            ];
        });

        return response()->json($result);
    }

    // 🔹 Resumo de pagamentos por forma para uma empresa/filial (opcional período)
    // Agrupa tb_pdv_pagamentos por forma_pagamento e soma os valores
    public function pagamentosResumo(Request $request)
    {
        $request->validate([
            'id_empresa' => 'required|integer',
            'id_filial' => 'sometimes|integer',
            'data_inicio' => 'sometimes|date',
            'data_fim' => 'sometimes|date'
        ]);

        $idEmpresa = $request->id_empresa;

        $pagamentosQuery = DB::table('tb_pdv_pagamentos as p')
            ->select('p.forma_pagamento', DB::raw('SUM(p.valor_pago) as total_pago'), DB::raw('COUNT(*) as total_pagamentos'))
            ->join('tb_pdv_vendas as v', 'p.id_venda', '=', 'v.id_venda')
            ->where('v.id_empresa', $idEmpresa)
            ->when($request->filled('id_filial'), function ($q) use ($request) {
                return $q->where('v.id_filial', $request->id_filial);
            })
            ->when($request->filled('data_inicio'), function ($q) use ($request) {
                return $q->whereDate('v.data_venda', '>=', $request->data_inicio);
            })
            ->when($request->filled('data_fim'), function ($q) use ($request) {
                return $q->whereDate('v.data_venda', '<=', $request->data_fim);
            })
            ->groupBy('p.forma_pagamento');

        $pagamentosPorForma = $pagamentosQuery->get();

        $totalGeral = $pagamentosQuery->clone()->select(DB::raw('SUM(p.valor_pago) as total_geral'))->first();

        return response()->json([
            'id_empresa' => (int) $idEmpresa,
            'id_filial' => $request->id_filial ?? null,
            'periodo' => [
                'inicio' => $request->data_inicio ?? null,
                'fim' => $request->data_fim ?? null
            ],
            'total_geral' => number_format($totalGeral->total_geral ?? 0, 2, '.', ''),
            'por_forma' => $pagamentosPorForma
        ]);
    }
}



