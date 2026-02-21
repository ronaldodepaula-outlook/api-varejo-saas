<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DashboardIndicadoresController extends Controller
{
    private function resolveEmpresaId(Request $request): int
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');
        if (!$empresaId) {
            throw ValidationException::withMessages([
                'id_empresa' => ['id_empresa e obrigatorio quando nao ha usuario autenticado.'],
            ]);
        }
        return (int) $empresaId;
    }

    private function resolvePeriodo(Request $request): array
    {
        $filtro = $request->get('filtro', 'mensal');
        $dataInicio = $request->get('data_inicio');
        $dataFim = $request->get('data_fim');

        if ($dataInicio || $dataFim) {
            $filtro = 'personalizado';
        }

        switch ($filtro) {
            case 'diario':
                $inicio = now()->startOfDay();
                $fim = now()->endOfDay();
                break;
            case 'semanal':
                $inicio = now()->subDays(6)->startOfDay();
                $fim = now()->endOfDay();
                break;
            case 'anual':
                $inicio = now()->startOfYear();
                $fim = now()->endOfYear();
                break;
            case 'personalizado':
                if (!$dataInicio || !$dataFim) {
                    throw ValidationException::withMessages([
                        'data_inicio' => ['data_inicio e data_fim sao obrigatorios para filtro personalizado.'],
                        'data_fim' => ['data_inicio e data_fim sao obrigatorios para filtro personalizado.'],
                    ]);
                }
                $inicio = Carbon::parse($dataInicio)->startOfDay();
                $fim = Carbon::parse($dataFim)->endOfDay();
                break;
            case 'mensal':
            default:
                $inicio = now()->startOfMonth();
                $fim = now()->endOfMonth();
                $filtro = 'mensal';
                break;
        }

        return [
            'filtro' => $filtro,
            'inicio' => $inicio,
            'fim' => $fim,
            'range_date' => [$inicio->toDateString(), $fim->toDateString()],
            'range_datetime' => [$inicio->toDateTimeString(), $fim->toDateTimeString()],
        ];
    }

    private function applyFilial($query, ?int $idFilial, string $coluna = 'id_filial')
    {
        if ($idFilial) {
            $query->where($coluna, $idFilial);
        }
        return $query;
    }

    public function indicadores(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;
        $periodo = $this->resolvePeriodo($request);

        $rangeDate = $periodo['range_date'];
        $rangeDateTime = $periodo['range_datetime'];

        // Vendas PDV
        $pdvBase = DB::table('tb_pdv_vendas')->where('id_empresa', $empresaId);
        $this->applyFilial($pdvBase, $filialId);

        $pdvPeriodo = (clone $pdvBase)->whereBetween('data_venda', $rangeDateTime);
        $pdvVendas = (clone $pdvPeriodo)->where('status', 'fechada')->where('tipo_venda', 'venda');
        $pdvCount = (clone $pdvVendas)->count();
        $pdvTotal = (clone $pdvVendas)->sum('valor_total');

        // Vendas assistidas
        $vaBase = DB::table('tb_vendas_assistidas')->where('id_empresa', $empresaId);
        $this->applyFilial($vaBase, $filialId);

        $vaPeriodo = (clone $vaBase)->whereBetween('data_venda', $rangeDateTime);
        $vaVendas = (clone $vaPeriodo)->where('status', 'finalizada')->where('tipo_venda', 'venda');
        $vaCount = (clone $vaVendas)->count();
        $vaTotal = (clone $vaVendas)->sum('valor_total');

        $vaFiado = (clone $vaVendas)->where('forma_pagamento', 'fiado');
        $vaFiadoCount = (clone $vaFiado)->count();
        $vaFiadoTotal = (clone $vaFiado)->sum('valor_total');

        // Pagamentos PDV
        $pagamentos = DB::table('tb_pdv_pagamentos as pg')
            ->join('tb_pdv_vendas as v', 'pg.id_venda', '=', 'v.id_venda')
            ->where('v.id_empresa', $empresaId)
            ->when($filialId, function ($q) use ($filialId) {
                $q->where('v.id_filial', $filialId);
            })
            ->whereBetween('pg.data_pagamento', $rangeDateTime);

        $pagamentosTotal = (clone $pagamentos)->sum('pg.valor_pago');
        $pagamentosPorForma = (clone $pagamentos)
            ->select('pg.forma_pagamento', DB::raw('SUM(pg.valor_pago) as total'), DB::raw('COUNT(*) as quantidade'))
            ->groupBy('pg.forma_pagamento')
            ->get();

        $pdvFiado = DB::table('tb_pdv_pagamentos as pg')
            ->join('tb_pdv_vendas as v', 'pg.id_venda', '=', 'v.id_venda')
            ->where('v.id_empresa', $empresaId)
            ->when($filialId, function ($q) use ($filialId) {
                $q->where('v.id_filial', $filialId);
            })
            ->whereBetween('v.data_venda', $rangeDateTime)
            ->where('pg.forma_pagamento', 'fiado')
            ->selectRaw('COUNT(DISTINCT v.id_venda) as quantidade, COALESCE(SUM(v.valor_total),0) as valor_total')
            ->first();

        $totalVendas = $pdvTotal + $vaTotal;
        $totalVendasCount = $pdvCount + $vaCount;
        $ticketMedio = $totalVendasCount > 0 ? round($totalVendas / $totalVendasCount, 2) : 0;

        // Compras
        $cotacoes = DB::table('tb_cotacoes_cabecalho')->where('id_empresa', $empresaId);
        $this->applyFilial($cotacoes, $filialId);
        $cotacoesCount = (clone $cotacoes)->whereBetween('data_cotacao', $rangeDate)->count();

        $pedidos = DB::table('tb_pedidos_compra_cabecalho')->where('id_empresa', $empresaId);
        $this->applyFilial($pedidos, $filialId);
        $pedidosPeriodo = (clone $pedidos)->whereBetween('data_pedido', $rangeDate);
        $pedidosCount = (clone $pedidosPeriodo)->count();
        $pedidosValor = (clone $pedidosPeriodo)->sum('valor_total');
        $pedidosPorStatus = (clone $pedidosPeriodo)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        $entradas = DB::table('tb_entradas_compra_cabecalho')->where('id_empresa', $empresaId);
        $this->applyFilial($entradas, $filialId);
        $entradasPeriodo = (clone $entradas)
            ->whereIn('status', ['recebido', 'conferido'])
            ->whereBetween('data_recebimento', $rangeDateTime);
        $entradasCount = (clone $entradasPeriodo)->count();
        $entradasValor = (clone $entradasPeriodo)->sum('valor_total');

        // Estoque
        $estoque = DB::table('tb_estoque as e')
            ->join('tb_produtos as p', 'e.id_produto', '=', 'p.id_produto')
            ->where('e.id_empresa', $empresaId);
        $this->applyFilial($estoque, $filialId, 'e.id_filial');

        $estoqueValorCusto = (clone $estoque)->selectRaw('COALESCE(SUM(e.quantidade * p.preco_custo),0) as total')->value('total');
        $estoqueValorVenda = (clone $estoque)->selectRaw('COALESCE(SUM(e.quantidade * p.preco_venda),0) as total')->value('total');
        $estoqueItens = (clone $estoque)->where('e.quantidade', '>', 0)->distinct('e.id_produto')->count('e.id_produto');
        $estoqueTotalQtd = (clone $estoque)->sum('e.quantidade');
        $estoqueReservado = (clone $estoque)->sum('e.quantidade_reservada');
        $estoquePendencia = (clone $estoque)->sum('e.pendencia_compra');

        // Movimentacoes
        $mov = DB::table('tb_movimentacoes')->where('id_empresa', $empresaId);
        $this->applyFilial($mov, $filialId);
        $movPeriodo = (clone $mov)->whereBetween('data_movimentacao', $rangeDateTime);
        $movEntradas = (clone $movPeriodo)->whereIn('tipo_movimentacao', ['entrada', 'ajuste'])->sum('quantidade');
        $movSaidas = (clone $movPeriodo)->whereIn('tipo_movimentacao', ['saida', 'transferencia'])->sum('quantidade');
        $movTotal = (clone $movPeriodo)->count();

        // Transferencias
        $transf = DB::table('tb_transferencias')->where('id_empresa', $empresaId);
        if ($filialId) {
            $transf->where(function ($q) use ($filialId) {
                $q->where('id_filial_origem', $filialId)->orWhere('id_filial_destino', $filialId);
            });
        }
        $transfPeriodo = (clone $transf)->whereBetween('data_transferencia', $rangeDate);
        $transfTotal = (clone $transfPeriodo)->count();
        $transfPorStatus = (clone $transfPeriodo)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        // Inventario
        $capas = DB::table('tb_capa_inventario')->where('id_empresa', $empresaId);
        $this->applyFilial($capas, $filialId);
        $capasPeriodo = (clone $capas)->whereBetween('data_inicio', $rangeDate);
        $capasTotal = (clone $capasPeriodo)->count();
        $capasPorStatus = (clone $capasPeriodo)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        $inventario = DB::table('tb_inventario')->where('id_empresa', $empresaId);
        $this->applyFilial($inventario, $filialId);
        $inventarioPeriodo = (clone $inventario)->whereBetween('data_inventario', $rangeDateTime);
        $inventarioTotal = (clone $inventarioPeriodo)->count();
        $inventarioDivergencias = (clone $inventarioPeriodo)->where('diferenca', '!=', 0)->count();

        // Financeiro
        $contasReceber = DB::table('tb_contas_receber')->where('id_empresa', $empresaId);
        $this->applyFilial($contasReceber, $filialId);
        $contasReceberPeriodo = (clone $contasReceber)->whereBetween('data_emissao', $rangeDate);
        $receberAberta = (clone $contasReceberPeriodo)->whereIn('status', ['aberta', 'parcial', 'atrasada'])->count();
        $receberAtrasada = (clone $contasReceberPeriodo)->where('status', 'atrasada')->count();
        $receberQuitada = (clone $contasReceberPeriodo)->where('status', 'quitada')->count();
        $receberValorAberto = (clone $contasReceberPeriodo)
            ->whereIn('status', ['aberta', 'parcial', 'atrasada'])
            ->selectRaw('COALESCE(SUM(valor_total - valor_recebido),0) as total')
            ->value('total');

        $contasPagar = DB::table('tb_contas_pagar')->where('id_empresa', $empresaId);
        $this->applyFilial($contasPagar, $filialId);
        $contasPagarPeriodo = (clone $contasPagar)->whereBetween('data_emissao', $rangeDate);
        $pagarAberta = (clone $contasPagarPeriodo)->whereIn('status', ['aberta', 'parcial', 'atrasada'])->count();
        $pagarAtrasada = (clone $contasPagarPeriodo)->where('status', 'atrasada')->count();
        $pagarQuitada = (clone $contasPagarPeriodo)->where('status', 'quitada')->count();
        $pagarValorAberto = (clone $contasPagarPeriodo)
            ->whereIn('status', ['aberta', 'parcial', 'atrasada'])
            ->selectRaw('COALESCE(SUM(valor_total - valor_pago),0) as total')
            ->value('total');

        // Precificacao
        $promocoesAtivas = DB::table('tb_promocoes_cabecalho')
            ->where('id_empresa', $empresaId)
            ->when($filialId, function ($q) use ($filialId) {
                $q->where('id_filial', $filialId);
            })
            ->where('status', 'ativa')
            ->where('data_inicio', '<=', $periodo['fim'])
            ->where('data_fim', '>=', $periodo['inicio'])
            ->count();

        $produtosPromocao = DB::table('tb_promocoes_produtos as pp')
            ->join('tb_promocoes_cabecalho as pc', 'pp.id_promocao', '=', 'pc.id_promocao')
            ->where('pc.id_empresa', $empresaId)
            ->when($filialId, function ($q) use ($filialId) {
                $q->where('pc.id_filial', $filialId);
            })
            ->where('pc.status', 'ativa')
            ->where('pc.data_inicio', '<=', $periodo['fim'])
            ->where('pc.data_fim', '>=', $periodo['inicio'])
            ->distinct('pp.id_produto')
            ->count('pp.id_produto');

        $reajustesPeriodo = DB::table('tb_precos_historico')
            ->where('id_empresa', $empresaId)
            ->whereBetween('data_alteracao', $rangeDateTime)
            ->count();

        $lotesProcessados = DB::table('tb_atualizacao_precos_cabecalho')
            ->where('id_empresa', $empresaId)
            ->when($filialId, function ($q) use ($filialId) {
                $q->where('id_filial', $filialId);
            })
            ->where('status', 'processado')
            ->whereBetween('data_atualizacao', $rangeDate)
            ->count();

        // Cadastros
        $produtosAtivos = DB::table('tb_produtos')
            ->where('id_empresa', $empresaId)
            ->where('ativo', 1)
            ->count();

        $produtosNovos = DB::table('tb_produtos')
            ->where('id_empresa', $empresaId)
            ->whereBetween('created_at', $rangeDateTime)
            ->count();

        $clientesAtivos = DB::table('tb_clientes')
            ->where('id_empresa', $empresaId)
            ->where(function ($q) {
                $q->where('status', 'ativo')->orWhereNull('status');
            })
            ->count();

        $clientesNovos = DB::table('tb_clientes')
            ->where('id_empresa', $empresaId)
            ->whereBetween('data_cadastro', $rangeDate)
            ->count();

        $fornecedoresAtivos = DB::table('tb_fornecedores')
            ->where('id_empresa', $empresaId)
            ->where(function ($q) {
                $q->where('status', 'ativo')->orWhereNull('status');
            })
            ->count();

        $fornecedoresNovos = DB::table('tb_fornecedores')
            ->where('id_empresa', $empresaId)
            ->whereBetween('created_at', $rangeDateTime)
            ->count();

        return response()->json([
            'success' => true,
            'params' => [
                'id_empresa' => $empresaId,
                'id_filial' => $filialId,
                'filtro' => $periodo['filtro'],
                'data_inicio' => $periodo['inicio']->toDateTimeString(),
                'data_fim' => $periodo['fim']->toDateTimeString(),
            ],
            'vendas' => [
                'total' => [
                    'quantidade' => (int) $totalVendasCount,
                    'valor' => (float) $totalVendas,
                    'ticket_medio' => (float) $ticketMedio,
                ],
                'pdv' => [
                    'quantidade' => (int) $pdvCount,
                    'valor' => (float) $pdvTotal,
                ],
                'assistidas' => [
                    'quantidade' => (int) $vaCount,
                    'valor' => (float) $vaTotal,
                ],
                'fiado' => [
                    'pdv' => [
                        'quantidade' => (int) ($pdvFiado->quantidade ?? 0),
                        'valor' => (float) ($pdvFiado->valor_total ?? 0),
                    ],
                    'assistidas' => [
                        'quantidade' => (int) $vaFiadoCount,
                        'valor' => (float) $vaFiadoTotal,
                    ],
                ],
            ],
            'pagamentos' => [
                'total_recebido' => (float) $pagamentosTotal,
                'por_forma' => $pagamentosPorForma,
            ],
            'compras' => [
                'cotacoes' => (int) $cotacoesCount,
                'pedidos' => [
                    'quantidade' => (int) $pedidosCount,
                    'valor' => (float) $pedidosValor,
                    'por_status' => $pedidosPorStatus,
                ],
                'entradas' => [
                    'quantidade' => (int) $entradasCount,
                    'valor' => (float) $entradasValor,
                ],
            ],
            'estoque' => [
                'valor_custo' => (float) $estoqueValorCusto,
                'valor_venda' => (float) $estoqueValorVenda,
                'quantidade_total' => (float) $estoqueTotalQtd,
                'itens_com_estoque' => (int) $estoqueItens,
                'reservado' => (float) $estoqueReservado,
                'pendencia_compra' => (float) $estoquePendencia,
            ],
            'movimentacoes' => [
                'total' => (int) $movTotal,
                'entradas' => (float) $movEntradas,
                'saidas' => (float) $movSaidas,
            ],
            'transferencias' => [
                'total' => (int) $transfTotal,
                'por_status' => $transfPorStatus,
            ],
            'inventario' => [
                'capas' => [
                    'total' => (int) $capasTotal,
                    'por_status' => $capasPorStatus,
                ],
                'itens' => [
                    'total' => (int) $inventarioTotal,
                    'divergencias' => (int) $inventarioDivergencias,
                ],
            ],
            'financeiro' => [
                'contas_receber' => [
                    'abertas' => (int) $receberAberta,
                    'atrasadas' => (int) $receberAtrasada,
                    'quitadas' => (int) $receberQuitada,
                    'valor_em_aberto' => (float) $receberValorAberto,
                ],
                'contas_pagar' => [
                    'abertas' => (int) $pagarAberta,
                    'atrasadas' => (int) $pagarAtrasada,
                    'quitadas' => (int) $pagarQuitada,
                    'valor_em_aberto' => (float) $pagarValorAberto,
                ],
            ],
            'precificacao' => [
                'promocoes_ativas' => (int) $promocoesAtivas,
                'produtos_em_promocao' => (int) $produtosPromocao,
                'reajustes_periodo' => (int) $reajustesPeriodo,
                'lotes_processados' => (int) $lotesProcessados,
            ],
            'cadastros' => [
                'produtos_ativos' => (int) $produtosAtivos,
                'produtos_novos' => (int) $produtosNovos,
                'clientes_ativos' => (int) $clientesAtivos,
                'clientes_novos' => (int) $clientesNovos,
                'fornecedores_ativos' => (int) $fornecedoresAtivos,
                'fornecedores_novos' => (int) $fornecedoresNovos,
            ],
        ]);
    }
}
