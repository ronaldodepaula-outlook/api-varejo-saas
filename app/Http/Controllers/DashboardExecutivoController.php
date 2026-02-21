<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DashboardExecutivoController extends Controller
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

    public function metricas(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;

        $hoje = now()->toDateString();
        $mesInicio = now()->startOfMonth()->toDateString();
        $mesFim = now()->endOfMonth()->toDateString();

        $pdvBase = DB::table('tb_pdv_vendas')->where('id_empresa', $empresaId);
        $this->applyFilial($pdvBase, $filialId);

        $pdvDia = (clone $pdvBase)
            ->whereDate('data_venda', $hoje)
            ->where('status', 'fechada')
            ->where('tipo_venda', 'venda');

        $vendasDiaQuantidade = (clone $pdvDia)->count();
        $vendasDiaValor = (clone $pdvDia)->sum('valor_total');

        $pdvMes = (clone $pdvBase)
            ->whereBetween('data_venda', ["{$mesInicio} 00:00:00", "{$mesFim} 23:59:59"])
            ->where('status', 'fechada')
            ->where('tipo_venda', 'venda');

        $vendasMesQuantidade = (clone $pdvMes)->count();
        $vendasMesValor = (clone $pdvMes)->sum('valor_total');
        $ticketMedio = $vendasMesQuantidade > 0 ? round($vendasMesValor / $vendasMesQuantidade, 2) : 0;

        $clientesAtivos = DB::table('tb_clientes')
            ->where('id_empresa', $empresaId)
            ->where('flag_excluido_logico', 0)
            ->where(function ($q) {
                $q->where('status', 'ativo')->orWhereNull('status');
            })
            ->count();

        $produtosAtivos = DB::table('tb_produtos')
            ->where('id_empresa', $empresaId)
            ->where('ativo', 1)
            ->count();

        $estoque = DB::table('tb_estoque')->where('id_empresa', $empresaId);
        $this->applyFilial($estoque, $filialId);

        $estoqueBaixo = (clone $estoque)->whereColumn('quantidade', '<=', 'estoque_minimo')->count();
        $pendenciasCompra = (clone $estoque)->sum('pendencia_compra');

        $promocoes = DB::table('tb_promocoes_cabecalho')
            ->where('id_empresa', $empresaId)
            ->where('status', 'ativa')
            ->where('data_fim', '>=', now());

        if ($filialId) {
            $promocoes->where(function ($q) use ($filialId) {
                $q->where('id_filial', $filialId)->orWhere('aplicar_em_todas_filiais', 1);
            });
        }

        $promocoesAtivas = $promocoes->count();

        return response()->json([
            'success' => true,
            'data' => [
                'vendas_dia_quantidade' => (int) $vendasDiaQuantidade,
                'vendas_dia_valor' => (float) $vendasDiaValor,
                'vendas_mes_quantidade' => (int) $vendasMesQuantidade,
                'vendas_mes_valor' => (float) $vendasMesValor,
                'ticket_medio' => (float) $ticketMedio,
                'clientes_ativos' => (int) $clientesAtivos,
                'produtos_ativos' => (int) $produtosAtivos,
                'estoque_baixo' => (int) $estoqueBaixo,
                'total_pendencias_compra' => (float) $pendenciasCompra,
                'promocoes_ativas' => (int) $promocoesAtivas,
            ]
        ]);
    }

    public function vendasPeriodo(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;
        $periodo = $this->resolvePeriodo($request);

        $base = DB::table('tb_pdv_vendas')
            ->where('id_empresa', $empresaId)
            ->where('status', 'fechada')
            ->where('tipo_venda', 'venda');

        $this->applyFilial($base, $filialId);

        $rows = $base
            ->whereBetween('data_venda', $periodo['range_datetime'])
            ->selectRaw('DATE(data_venda) as data, COUNT(*) as quantidade_vendas, ROUND(SUM(valor_total),2) as valor_total')
            ->groupBy(DB::raw('DATE(data_venda)'))
            ->orderBy('data')
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function vendasPorFilial(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;
        $periodo = $this->resolvePeriodo($request);

        $base = DB::table('tb_pdv_vendas as v')
            ->join('tb_filiais as f', 'v.id_filial', '=', 'f.id_filial')
            ->where('v.id_empresa', $empresaId)
            ->where('v.status', 'fechada')
            ->where('v.tipo_venda', 'venda');

        if ($filialId) {
            $base->where('v.id_filial', $filialId);
        }

        $rows = $base
            ->whereBetween('v.data_venda', $periodo['range_datetime'])
            ->groupBy('f.id_filial', 'f.nome_filial')
            ->select(
                'f.nome_filial',
                DB::raw('COUNT(v.id_venda) as total_vendas'),
                DB::raw('ROUND(SUM(v.valor_total),2) as valor_total'),
                DB::raw('ROUND(AVG(v.valor_total),2) as ticket_medio')
            )
            ->orderByDesc('valor_total')
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function formasPagamento(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;
        $periodo = $this->resolvePeriodo($request);

        $base = DB::table('tb_pdv_pagamentos as p')
            ->join('tb_pdv_vendas as v', 'p.id_venda', '=', 'v.id_venda')
            ->where('v.id_empresa', $empresaId)
            ->whereBetween('p.data_pagamento', $periodo['range_datetime']);

        if ($filialId) {
            $base->where('v.id_filial', $filialId);
        }

        $rows = $base
            ->select(
                'p.forma_pagamento',
                DB::raw('COUNT(*) as quantidade'),
                DB::raw('ROUND(SUM(p.valor_pago),2) as valor_total')
            )
            ->groupBy('p.forma_pagamento')
            ->orderByDesc('valor_total')
            ->get();

        $total = $rows->sum('valor_total');
        $rows = $rows->map(function ($row) use ($total) {
            $percentual = $total > 0 ? round(($row->valor_total / $total) * 100, 2) : 0;
            return [
                'forma_pagamento' => $row->forma_pagamento,
                'quantidade' => (int) $row->quantidade,
                'valor_total' => (float) $row->valor_total,
                'percentual' => (float) $percentual,
            ];
        });

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function contasReceberResumo(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;
        $periodo = $this->resolvePeriodo($request);

        $base = DB::table('tb_contas_receber')
            ->where('id_empresa', $empresaId)
            ->whereBetween('data_emissao', $periodo['range_date'])
            ->whereIn('status', ['aberta', 'parcial', 'atrasada']);

        $this->applyFilial($base, $filialId);

        $rows = $base
            ->select(
                'status',
                DB::raw('COUNT(*) as quantidade'),
                DB::raw('ROUND(SUM(valor_total - valor_recebido),2) as saldo_devedor'),
                DB::raw('ROUND(SUM(valor_recebido),2) as total_recebido'),
                DB::raw('ROUND(SUM(valor_total),2) as total_geral')
            )
            ->groupBy('status')
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function contasPagarResumo(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;
        $periodo = $this->resolvePeriodo($request);

        $base = DB::table('tb_contas_pagar')
            ->where('id_empresa', $empresaId)
            ->whereBetween('data_emissao', $periodo['range_date'])
            ->whereIn('status', ['aberta', 'parcial', 'atrasada']);

        $this->applyFilial($base, $filialId);

        $rows = $base
            ->select(
                'status',
                DB::raw('COUNT(*) as quantidade'),
                DB::raw('ROUND(SUM(valor_total - valor_pago),2) as saldo_pendente'),
                DB::raw('ROUND(SUM(valor_pago),2) as total_pago'),
                DB::raw('ROUND(SUM(valor_total),2) as total_geral')
            )
            ->groupBy('status')
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function fluxoCaixa(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;
        $meses = (int) $request->get('meses', 6);
        if ($meses <= 0) {
            $meses = 6;
        }

        $inicio = now()->startOfMonth();
        $fim = now()->addMonths($meses - 1)->endOfMonth();

        $filialClause = '';
        if ($filialId) {
            $filialClause = ' AND id_filial = ?';
        }

        $params = [
            $empresaId,
            $inicio->toDateString(),
            $fim->toDateString(),
        ];
        if ($filialId) {
            $params[] = $filialId;
        }
        $params = array_merge($params, [
            $empresaId,
            $inicio->toDateString(),
            $fim->toDateString(),
        ]);
        if ($filialId) {
            $params[] = $filialId;
        }

        $sql = "
            SELECT
                DATE_FORMAT(data_vencimento, '%Y-%m') AS mes,
                SUM(CASE WHEN tipo = 'receber' THEN valor ELSE 0 END) AS a_receber,
                SUM(CASE WHEN tipo = 'pagar' THEN valor ELSE 0 END) AS a_pagar,
                SUM(CASE WHEN tipo = 'receber' THEN valor ELSE -valor END) AS saldo_projetado
            FROM (
                SELECT
                    data_vencimento,
                    (valor_total - valor_recebido) AS valor,
                    'receber' AS tipo
                FROM tb_contas_receber
                WHERE id_empresa = ?
                  AND status IN ('aberta', 'parcial')
                  AND data_vencimento BETWEEN ? AND ?
                  {$filialClause}
                UNION ALL
                SELECT
                    data_vencimento,
                    (valor_total - valor_pago) AS valor,
                    'pagar' AS tipo
                FROM tb_contas_pagar
                WHERE id_empresa = ?
                  AND status IN ('aberta', 'parcial')
                  AND data_vencimento BETWEEN ? AND ?
                  {$filialClause}
            ) AS fluxo
            GROUP BY DATE_FORMAT(data_vencimento, '%Y-%m')
            ORDER BY mes
        ";

        $rows = DB::select($sql, $params);

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function topProdutos(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;
        $periodo = $this->resolvePeriodo($request);
        $limit = (int) $request->get('limit', 10);
        if ($limit <= 0) {
            $limit = 10;
        }

        $base = DB::table('tb_itens_venda_assistida as i')
            ->join('tb_vendas_assistidas as v', 'i.id_venda', '=', 'v.id_venda')
            ->join('tb_produtos as p', 'i.id_produto', '=', 'p.id_produto')
            ->where('i.id_empresa', $empresaId)
            ->whereBetween('v.data_venda', $periodo['range_datetime']);

        if ($filialId) {
            $base->where('i.id_filial', $filialId);
        }

        $rows = $base
            ->groupBy('p.id_produto', 'p.descricao', 'p.codigo_barras')
            ->select(
                'p.id_produto',
                'p.descricao as produto',
                'p.codigo_barras',
                DB::raw('SUM(i.quantidade) as quantidade_vendida'),
                DB::raw('ROUND(SUM(i.quantidade * COALESCE(i.valor_unitario,0)),2) as valor_total_vendas'),
                DB::raw('COUNT(DISTINCT i.id_venda) as numero_vendas')
            )
            ->orderByDesc('quantidade_vendida')
            ->limit($limit)
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function estoqueCritico(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;

        $base = DB::table('tb_estoque as e')
            ->join('tb_empresas as emp', 'e.id_empresa', '=', 'emp.id_empresa')
            ->join('tb_filiais as f', 'e.id_filial', '=', 'f.id_filial')
            ->join('tb_produtos as p', 'e.id_produto', '=', 'p.id_produto')
            ->where('e.id_empresa', $empresaId)
            ->whereColumn('e.quantidade', '<=', 'e.estoque_minimo');

        if ($filialId) {
            $base->where('e.id_filial', $filialId);
        }

        $rows = $base
            ->select(
                'e.id_empresa',
                'emp.nome_empresa',
                'e.id_filial',
                'f.nome_filial',
                'e.id_produto',
                'p.descricao as produto',
                'p.codigo_barras',
                'e.quantidade as estoque_atual',
                'e.estoque_minimo',
                'e.estoque_maximo',
                'e.pendencia_compra',
                DB::raw('(e.quantidade + e.pendencia_compra) as estoque_futuro'),
                DB::raw("CASE
                    WHEN e.quantidade <= 0 THEN 'CRITICO - SEM ESTOQUE'
                    WHEN e.quantidade <= e.estoque_minimo / 2 THEN 'URGENTE'
                    WHEN e.quantidade <= e.estoque_minimo THEN 'ATENCAO'
                    ELSE 'NORMAL'
                END as nivel_alerta"),
                DB::raw("CASE
                    WHEN e.pendencia_compra > 0 THEN CONCAT(e.pendencia_compra, ' pendente')
                    ELSE 'Sem pedido'
                END as status_compra")
            )
            ->orderByRaw('(e.quantidade / NULLIF(e.estoque_minimo,0)) ASC')
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function pedidosPendentes(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;

        $base = DB::table('tb_pedidos_compra_cabecalho as pc')
            ->join('tb_fornecedores as f', 'pc.id_fornecedor', '=', 'f.id_fornecedor')
            ->join('tb_pedidos_compra_itens as pi', 'pc.id_pedido', '=', 'pi.id_pedido')
            ->where('pc.id_empresa', $empresaId)
            ->whereIn('pc.status', ['enviado', 'parcial'])
            ->whereRaw('pi.quantidade > pi.quantidade_recebida');

        if ($filialId) {
            $base->where('pc.id_filial', $filialId);
        }

        $rows = $base
            ->groupBy('pc.id_pedido', 'pc.numero_pedido', 'pc.data_pedido', 'pc.data_previsao_entrega', 'f.razao_social')
            ->select(
                'pc.id_pedido',
                'pc.numero_pedido',
                'pc.data_pedido',
                'pc.data_previsao_entrega',
                'f.razao_social as fornecedor',
                DB::raw('COUNT(pi.id_item_pedido) as total_itens'),
                DB::raw('ROUND(SUM(pi.quantidade - pi.quantidade_recebida), 2) as itens_pendentes'),
                DB::raw('ROUND(SUM((pi.quantidade - pi.quantidade_recebida) * pi.preco_unitario), 2) as valor_pendente'),
                DB::raw('DATEDIFF(pc.data_previsao_entrega, CURDATE()) as dias_para_entrega'),
                DB::raw("CASE
                    WHEN pc.data_previsao_entrega < CURDATE() THEN 'ATRASADO'
                    WHEN DATEDIFF(pc.data_previsao_entrega, CURDATE()) <= 3 THEN 'PROXIMO'
                    ELSE 'NO PRAZO'
                END as status_entrega")
            )
            ->orderByRaw("CASE
                WHEN pc.data_previsao_entrega < CURDATE() THEN 1
                WHEN DATEDIFF(pc.data_previsao_entrega, CURDATE()) <= 3 THEN 2
                ELSE 3
            END")
            ->orderBy('pc.data_previsao_entrega')
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function entradasRecentes(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;
        $periodo = $this->resolvePeriodo($request);
        $limit = (int) $request->get('limit', 10);
        if ($limit <= 0) {
            $limit = 10;
        }

        $base = DB::table('tb_entradas_compra_cabecalho as ec')
            ->join('tb_fornecedores as f', 'ec.id_fornecedor', '=', 'f.id_fornecedor')
            ->leftJoin('tb_pedidos_compra_cabecalho as pc', 'ec.id_pedido', '=', 'pc.id_pedido')
            ->join('tb_entradas_compra_itens as eci', 'ec.id_entrada', '=', 'eci.id_entrada')
            ->where('ec.id_empresa', $empresaId)
            ->whereBetween('ec.data_entrada', $periodo['range_date']);

        if ($filialId) {
            $base->where('ec.id_filial', $filialId);
        }

        $rows = $base
            ->groupBy(
                'ec.id_entrada',
                'ec.numero_entrada',
                'ec.data_entrada',
                'ec.data_recebimento',
                'ec.status',
                'f.razao_social',
                'pc.numero_pedido',
                'ec.valor_total'
            )
            ->select(
                'ec.id_entrada',
                'ec.numero_entrada',
                'ec.data_entrada',
                'ec.data_recebimento',
                'ec.status',
                'f.razao_social as fornecedor',
                'pc.numero_pedido',
                DB::raw('COUNT(eci.id_item_entrada) as itens_recebidos'),
                DB::raw('ROUND(ec.valor_total, 2) as valor_total')
            )
            ->orderByDesc('ec.data_entrada')
            ->limit($limit)
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function promocoesAtivas(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;

        $base = DB::table('tb_promocoes_cabecalho as pc')
            ->where('pc.id_empresa', $empresaId)
            ->where('pc.status', 'ativa')
            ->where('pc.data_fim', '>=', now());

        if ($filialId) {
            $base->where(function ($q) use ($filialId) {
                $q->where('pc.id_filial', $filialId)->orWhere('pc.aplicar_em_todas_filiais', 1);
            });
        }

        $base->select(
            'pc.id_promocao',
            'pc.codigo_promocao',
            'pc.nome_promocao',
            'pc.tipo_promocao',
            'pc.data_inicio',
            'pc.data_fim',
            DB::raw("CASE
                WHEN pc.data_fim < NOW() THEN 'Expirada'
                WHEN DATEDIFF(pc.data_fim, NOW()) <= 1 THEN 'Ultimo dia'
                WHEN DATEDIFF(pc.data_fim, NOW()) <= 3 THEN 'Acabando'
                ELSE 'Ativa'
            END as status_urgente"),
            DB::raw('DATEDIFF(pc.data_fim, NOW()) as dias_restantes')
        );

        $base->selectSub(function ($q) {
            $q->from('tb_promocoes_produtos')
                ->selectRaw('COUNT(*)')
                ->whereColumn('id_promocao', 'pc.id_promocao');
        }, 'total_produtos');

        $base->selectSub(function ($q) {
            $q->from('tb_promocoes_produtos')
                ->selectRaw('ROUND(AVG(desconto_percentual), 2)')
                ->whereColumn('id_promocao', 'pc.id_promocao');
        }, 'desconto_medio');

        $rows = $base
            ->orderBy('pc.data_fim')
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function produtosPromocao(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;

        $base = DB::table('tb_promocoes_produtos as pp')
            ->join('tb_promocoes_cabecalho as prom', 'pp.id_promocao', '=', 'prom.id_promocao')
            ->join('tb_produtos as p', 'pp.id_produto', '=', 'p.id_produto')
            ->where('prom.id_empresa', $empresaId)
            ->where('prom.status', 'ativa');

        if ($filialId) {
            $base->where('prom.id_filial', $filialId);
        }

        $rows = $base
            ->select(
                'pp.id_promocao_produto',
                'prom.nome_promocao',
                'p.id_produto',
                'p.descricao as produto',
                'p.codigo_barras',
                'p.unidade_medida',
                'p.preco_custo',
                'pp.preco_normal',
                'pp.preco_promocional',
                'pp.desconto_percentual',
                DB::raw('ROUND(((pp.preco_promocional - p.preco_custo) / NULLIF(p.preco_custo,0) * 100), 2) as margem_promocional'),
                'prom.data_fim',
                DB::raw('DATEDIFF(prom.data_fim, NOW()) as dias_restantes')
            )
            ->orderBy('prom.data_fim')
            ->orderByDesc('pp.desconto_percentual')
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function historicoPrecos(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $periodo = $this->resolvePeriodo($request);
        $limit = (int) $request->get('limit', 10);
        if ($limit <= 0) {
            $limit = 10;
        }

        $rows = DB::table('tb_precos_historico as ph')
            ->join('tb_empresas as e', 'ph.id_empresa', '=', 'e.id_empresa')
            ->join('tb_produtos as p', 'ph.id_produto', '=', 'p.id_produto')
            ->leftJoin('tb_usuarios as u', 'ph.id_usuario', '=', 'u.id_usuario')
            ->where('ph.id_empresa', $empresaId)
            ->whereBetween('ph.data_alteracao', $periodo['range_datetime'])
            ->select(
                'ph.data_alteracao',
                'e.nome_empresa',
                'p.descricao as produto',
                'ph.tipo_alteracao',
                'ph.preco_anterior',
                'ph.preco_novo',
                DB::raw("CONCAT(ph.percentual_ajuste, '%') as variacao"),
                'u.nome as usuario',
                'ph.motivo'
            )
            ->orderByDesc('ph.data_alteracao')
            ->limit($limit)
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function topClientes(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;
        $periodo = $this->resolvePeriodo($request);
        $limit = (int) $request->get('limit', 10);
        if ($limit <= 0) {
            $limit = 10;
        }

        $base = DB::table('tb_clientes as c')
            ->join('tb_vendas_assistidas as v', 'c.id_cliente', '=', 'v.id_cliente')
            ->where('c.id_empresa', $empresaId)
            ->where('c.flag_excluido_logico', 0)
            ->whereBetween('v.data_venda', $periodo['range_datetime']);

        if ($filialId) {
            $base->where('v.id_filial', $filialId);
        }

        $rows = $base
            ->groupBy('c.id_cliente', 'c.nome_cliente')
            ->select(
                'c.id_cliente',
                'c.nome_cliente',
                DB::raw('COUNT(v.id_venda) as total_compras'),
                DB::raw('ROUND(SUM(v.valor_total), 2) as valor_total_gasto'),
                DB::raw('ROUND(AVG(v.valor_total), 2) as ticket_medio'),
                DB::raw('MAX(v.data_venda) as ultima_compra'),
                DB::raw('DATEDIFF(NOW(), MAX(v.data_venda)) as dias_ultima_compra'),
                DB::raw("CASE
                    WHEN DATEDIFF(NOW(), MAX(v.data_venda)) <= 7 THEN 'Ativo'
                    WHEN DATEDIFF(NOW(), MAX(v.data_venda)) <= 30 THEN 'Regular'
                    ELSE 'Inativo'
                END as status_cliente")
            )
            ->orderByDesc('valor_total_gasto')
            ->limit($limit)
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function vendasAssistidasStatus(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;
        $periodo = $this->resolvePeriodo($request);

        $base = DB::table('tb_vendas_assistidas')
            ->where('id_empresa', $empresaId)
            ->whereBetween('data_venda', $periodo['range_datetime']);

        $this->applyFilial($base, $filialId);

        $rows = $base
            ->select('status', DB::raw('COUNT(*) as quantidade'), DB::raw('ROUND(SUM(valor_total),2) as valor_total'))
            ->groupBy('status')
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function debitosClientes(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);

        $rows = DB::table('tb_debitos_clientes as d')
            ->join('tb_clientes as c', 'd.id_cliente', '=', 'c.id_cliente')
            ->where('c.id_empresa', $empresaId)
            ->where('d.status', 'pendente')
            ->select(
                'd.id_debito',
                'c.nome_cliente',
                'c.telefone',
                'd.valor',
                'd.status',
                'd.data_geracao',
                DB::raw('DATEDIFF(NOW(), d.data_geracao) as dias_em_atraso'),
                DB::raw("CASE
                    WHEN DATEDIFF(NOW(), d.data_geracao) > 30 THEN 'CRITICO'
                    WHEN DATEDIFF(NOW(), d.data_geracao) > 15 THEN 'ATENCAO'
                    ELSE 'NORMAL'
                END as nivel_cobranca")
            )
            ->orderBy('d.data_geracao')
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function movimentacoesRecentes(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;
        $periodo = $this->resolvePeriodo($request);
        $limit = (int) $request->get('limit', 20);
        if ($limit <= 0) {
            $limit = 20;
        }

        $base = DB::table('tb_movimentacoes as m')
            ->join('tb_empresas as e', 'm.id_empresa', '=', 'e.id_empresa')
            ->join('tb_filiais as f', 'm.id_filial', '=', 'f.id_filial')
            ->join('tb_produtos as p', 'm.id_produto', '=', 'p.id_produto')
            ->leftJoin('tb_usuarios as u', 'm.id_usuario', '=', 'u.id_usuario')
            ->where('m.id_empresa', $empresaId)
            ->whereBetween('m.data_movimentacao', $periodo['range_datetime']);

        if ($filialId) {
            $base->where('m.id_filial', $filialId);
        }

        $rows = $base
            ->select(
                'm.data_movimentacao',
                'e.nome_empresa',
                'f.nome_filial',
                'p.descricao as produto',
                'm.tipo_movimentacao',
                'm.origem',
                'm.quantidade',
                'm.saldo_anterior',
                'm.saldo_atual',
                'm.custo_unitario',
                DB::raw('ROUND(m.quantidade * m.custo_unitario, 2) as valor_movimentado'),
                'u.nome as usuario'
            )
            ->orderByDesc('m.data_movimentacao')
            ->limit($limit)
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function movimentacoesResumo(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $filialId = $request->filled('id_filial') ? (int) $request->get('id_filial') : null;

        $inicio = now()->subMonths(5)->startOfMonth();

        $base = DB::table('tb_movimentacoes')
            ->where('id_empresa', $empresaId)
            ->where('data_movimentacao', '>=', $inicio->toDateTimeString());

        $this->applyFilial($base, $filialId);

        $rows = $base
            ->select(
                DB::raw("DATE_FORMAT(data_movimentacao, '%Y-%m') as mes"),
                'tipo_movimentacao',
                DB::raw('COUNT(*) as quantidade_movimentos'),
                DB::raw('SUM(quantidade) as total_quantidade'),
                DB::raw('ROUND(SUM(quantidade * custo_unitario), 2) as valor_total')
            )
            ->groupBy(DB::raw("DATE_FORMAT(data_movimentacao, '%Y-%m')"), 'tipo_movimentacao')
            ->orderByDesc('mes')
            ->orderBy('tipo_movimentacao')
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }
}
