<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class RelatorioKardexController extends Controller
{
    private function resolveParametros(Request $request): array
    {
        $data = $request->validate([
            'id_empresa' => 'nullable|integer|exists:tb_empresas,id_empresa',
            'id_filial' => 'required|integer|exists:tb_filiais,id_filial',
            'id_produto' => 'required|integer|exists:tb_produtos,id_produto',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
        ]);

        $empresaId = auth()->user()->id_empresa ?? ($data['id_empresa'] ?? null);
        if (!$empresaId) {
            throw ValidationException::withMessages([
                'id_empresa' => ['id_empresa é obrigatório quando não há usuário autenticado.'],
            ]);
        }

        $data['id_empresa'] = (int) $empresaId;
        $data['id_filial'] = (int) $data['id_filial'];
        $data['id_produto'] = (int) $data['id_produto'];

        $hasDataInicio = !empty($data['data_inicio']);
        $hasDataFim = !empty($data['data_fim']);

        if ($hasDataInicio && $hasDataFim && $data['data_fim'] < $data['data_inicio']) {
            throw ValidationException::withMessages([
                'data_fim' => ['data_fim deve ser maior ou igual a data_inicio.'],
            ]);
        }

        if (!$hasDataInicio || !$hasDataFim) {
            $range = DB::table('tb_movimentacoes as m')
                ->where('m.id_empresa', $data['id_empresa'])
                ->where('m.id_filial', $data['id_filial'])
                ->where('m.id_produto', $data['id_produto'])
                ->selectRaw('MIN(m.data_movimentacao) AS min_data, MAX(m.data_movimentacao) AS max_data')
                ->first();

            $defaultStart = $range && $range->min_data
                ? Carbon::parse($range->min_data)->startOfDay()
                : now()->startOfDay();
            $defaultEnd = $range && $range->max_data
                ? Carbon::parse($range->max_data)->endOfDay()
                : now()->endOfDay();

            if (!$hasDataInicio) {
                $data['data_inicio'] = $defaultStart->format('Y-m-d H:i:s');
            }
            if (!$hasDataFim) {
                $data['data_fim'] = $defaultEnd->format('Y-m-d H:i:s');
            }
        }

        if ($hasDataInicio) {
            $data['data_inicio'] = $this->normalizeDateTime($data['data_inicio'], true);
        }
        if ($hasDataFim) {
            $data['data_fim'] = $this->normalizeDateTime($data['data_fim'], false);
        }

        return $data;
    }

    private function normalizeDateTime(string $value, bool $isStart): string
    {
        $hasTime = strpos($value, ':') !== false;
        $dt = Carbon::parse($value);
        if (!$hasTime) {
            $dt = $isStart ? $dt->startOfDay() : $dt->endOfDay();
        }
        return $dt->format('Y-m-d H:i:s');
    }

    private function calcularSaldoAnterior(array $params): float
    {
        $saldo = DB::table('tb_movimentacoes as m')
            ->where('m.id_empresa', $params['id_empresa'])
            ->where('m.id_filial', $params['id_filial'])
            ->where('m.id_produto', $params['id_produto'])
            ->where('m.data_movimentacao', '<', $params['data_inicio'])
            ->selectRaw("COALESCE(SUM(CASE
                WHEN m.tipo_movimentacao IN ('entrada', 'ajuste') THEN m.quantidade
                WHEN m.tipo_movimentacao IN ('saida', 'transferencia') THEN -m.quantidade
                ELSE 0
            END), 0) AS saldo")
            ->value('saldo');

        return (float) $saldo;
    }

    public function kardex(Request $request)
    {
        $params = $this->resolveParametros($request);
        $saldoAnterior = $this->calcularSaldoAnterior($params);

        $sql = "
            SELECT
                e.nome_empresa,
                f.nome_filial,
                p.id_produto,
                p.descricao AS nome_produto,
                p.codigo_barras,
                p.unidade_medida,
                DATE_FORMAT(?, '%d/%m/%Y') AS data_inicio,
                DATE_FORMAT(?, '%d/%m/%Y') AS data_fim,
                ROUND(?, 2) AS saldo_anterior,
                DATE_FORMAT(m.data_movimentacao, '%d/%m/%Y %H:%i:%s') AS data_hora,
                UPPER(m.tipo_movimentacao) AS tipo_movimento,
                UPPER(m.origem) AS origem,
                CASE m.origem
                    WHEN 'nota_fiscal' THEN CONCAT('NF-e ', COALESCE(nf.nNF, '??'), ' Série ', COALESCE(nf.serie, '??'))
                    WHEN 'entrada_compra' THEN CONCAT('EC ', COALESCE(ec.numero_entrada, '??'))
                    WHEN 'venda_assistida' THEN CONCAT('Venda ', COALESCE(va.id_venda, '??'))
                    WHEN 'Ordem_de_Producao' THEN CONCAT('OP ', COALESCE(op.numero_ordem, '??'))
                    WHEN 'inventario' THEN CONCAT('Inventario ', COALESCE(ci.id_capa_inventario, '??'))
                    WHEN 'transferencia' THEN CONCAT('Transf ', COALESCE(t.id_transferencia, '??'))
                    ELSE CONCAT('Ref ', COALESCE(m.id_referencia, '0'))
                END AS documento,
                ROUND(CASE
                    WHEN m.tipo_movimentacao IN ('entrada', 'ajuste') AND m.quantidade > 0 THEN m.quantidade
                    ELSE 0
                END, 2) AS quantidade_entrada,
                ROUND(CASE
                    WHEN m.tipo_movimentacao IN ('saida', 'transferencia') AND m.quantidade > 0 THEN m.quantidade
                    ELSE 0
                END, 2) AS quantidade_saida,
                ROUND(m.saldo_atual, 2) AS saldo_atual,
                ROUND(m.custo_unitario, 2) AS custo_unitario,
                ROUND(m.saldo_atual * m.custo_unitario, 2) AS valor_total_estoque,
                COALESCE(u.nome, 'Sistema') AS usuario_responsavel,
                COALESCE(m.observacao, '-') AS observacao,
                CASE m.origem
                    WHEN 'nota_fiscal' THEN
                        CONCAT('Chave: ', COALESCE(nf.chave_acesso, 'N/A'),
                               ' | Valor: R$ ', COALESCE(nf.valor_total, 0))
                    WHEN 'entrada_compra' THEN
                        CONCAT('Fornecedor: ', COALESCE(fec.razao_social, 'N/A'),
                               ' | Tipo: ', ec.tipo_entrada,
                               ' | NF: ', COALESCE(ec.id_nfe, 'N/A'))
                    WHEN 'venda_assistida' THEN
                        CONCAT('Cliente: ', COALESCE(c.nome_cliente, 'N/A'),
                               ' | Pagamento: ', va.forma_pagamento,
                               ' | Total: R$ ', COALESCE(va.valor_total, 0))
                    WHEN 'Ordem_de_Producao' THEN
                        CONCAT('Ordem: ', op.numero_ordem,
                               ' | Status: ', op.status,
                               ' | Responsável: ', COALESCE(uop.nome, 'N/A'))
                    WHEN 'inventario' THEN
                        CONCAT('Descricao: ', ci.descricao,
                               ' | Status: ', ci.status,
                               ' | Data: ', DATE_FORMAT(ci.data_inicio, '%d/%m/%Y'))
                    WHEN 'transferencia' THEN
                        CONCAT('De: ', fo.nome_filial,
                               ' -> Para: ', fd.nome_filial)
                    ELSE ''
                END AS detalhes_adicionais
            FROM tb_empresas e
            CROSS JOIN tb_filiais f
            CROSS JOIN tb_produtos p
            LEFT JOIN tb_movimentacoes m ON m.id_empresa = e.id_empresa
                AND m.id_filial = f.id_filial
                AND m.id_produto = p.id_produto
                AND m.data_movimentacao BETWEEN ? AND ?
            LEFT JOIN tb_usuarios u ON m.id_usuario = u.id_usuario
            LEFT JOIN tb_nfe_cabecalho nf ON m.origem = 'nota_fiscal' AND m.id_referencia = nf.id_nfe
            LEFT JOIN tb_entradas_compra_cabecalho ec ON m.origem = 'entrada_compra' AND m.id_referencia = ec.id_entrada
            LEFT JOIN tb_fornecedores fec ON ec.id_fornecedor = fec.id_fornecedor
            LEFT JOIN tb_pedidos_compra_cabecalho pc ON ec.id_pedido = pc.id_pedido
            LEFT JOIN tb_vendas_assistidas va ON m.origem = 'venda_assistida' AND m.id_referencia = va.id_venda
            LEFT JOIN tb_clientes c ON va.id_cliente = c.id_cliente
            LEFT JOIN tb_ordens_producao op ON m.origem = 'Ordem_de_Producao' AND m.id_referencia = op.id_ordem_producao
            LEFT JOIN tb_usuarios uop ON op.responsavel_producao = uop.id_usuario
            LEFT JOIN tb_inventario i ON m.origem = 'inventario' AND m.id_referencia = i.id_inventario
            LEFT JOIN tb_capa_inventario ci ON i.id_capa_inventario = ci.id_capa_inventario
            LEFT JOIN tb_transferencias t ON m.origem = 'transferencia' AND m.id_referencia = t.id_transferencia
            LEFT JOIN tb_filiais fo ON t.id_filial_origem = fo.id_filial
            LEFT JOIN tb_filiais fd ON t.id_filial_destino = fd.id_filial
            WHERE e.id_empresa = ?
              AND f.id_filial = ?
              AND p.id_produto = ?
            ORDER BY m.data_movimentacao ASC
        ";

        $bindings = [
            $params['data_inicio'],
            $params['data_fim'],
            $saldoAnterior,
            $params['data_inicio'],
            $params['data_fim'],
            $params['id_empresa'],
            $params['id_filial'],
            $params['id_produto'],
        ];

        $rows = DB::select($sql, $bindings);

        return response()->json([
            'success' => true,
            'params' => $params,
            'saldo_anterior' => round($saldoAnterior, 2),
            'data' => $rows,
        ]);
    }

    public function kardexExport(Request $request)
    {
        $params = $this->resolveParametros($request);

        $sql = "
            SELECT
                DATE_FORMAT(m.data_movimentacao, '%d/%m/%Y %H:%i') AS 'Data/Hora',
                UPPER(m.tipo_movimentacao) AS 'Tipo',
                UPPER(m.origem) AS 'Origem',
                CASE m.origem
                    WHEN 'nota_fiscal' THEN CONCAT('NF-e ', nf.nNF)
                    WHEN 'entrada_compra' THEN CONCAT('EC ', ec.numero_entrada)
                    WHEN 'venda_assistida' THEN CONCAT('Venda ', va.id_venda)
                    WHEN 'Ordem_de_Producao' THEN CONCAT('OP ', op.numero_ordem)
                    WHEN 'inventario' THEN CONCAT('Inv ', i.id_inventario)
                    WHEN 'transferencia' THEN CONCAT('Transf ', t.id_transferencia)
                    ELSE m.id_referencia
                END AS 'Documento',
                ROUND(CASE WHEN m.tipo_movimentacao IN ('entrada','ajuste') THEN m.quantidade ELSE 0 END, 2) AS 'Entrada',
                ROUND(CASE WHEN m.tipo_movimentacao IN ('saida','transferencia') THEN m.quantidade ELSE 0 END, 2) AS 'Saida',
                ROUND(m.saldo_atual, 2) AS 'Saldo',
                ROUND(m.custo_unitario, 2) AS 'Custo Unit.',
                ROUND(m.saldo_atual * m.custo_unitario, 2) AS 'Valor Total',
                COALESCE(u.nome, 'Sistema') AS 'Usuario',
                m.observacao AS 'Observacao'
            FROM tb_movimentacoes m
            LEFT JOIN tb_usuarios u ON m.id_usuario = u.id_usuario
            LEFT JOIN tb_nfe_cabecalho nf ON m.origem = 'nota_fiscal' AND m.id_referencia = nf.id_nfe
            LEFT JOIN tb_entradas_compra_cabecalho ec ON m.origem = 'entrada_compra' AND m.id_referencia = ec.id_entrada
            LEFT JOIN tb_vendas_assistidas va ON m.origem = 'venda_assistida' AND m.id_referencia = va.id_venda
            LEFT JOIN tb_ordens_producao op ON m.origem = 'Ordem_de_Producao' AND m.id_referencia = op.id_ordem_producao
            LEFT JOIN tb_inventario i ON m.origem = 'inventario' AND m.id_referencia = i.id_inventario
            LEFT JOIN tb_transferencias t ON m.origem = 'transferencia' AND m.id_referencia = t.id_transferencia
            WHERE m.id_empresa = ?
              AND m.id_filial = ?
              AND m.id_produto = ?
              AND m.data_movimentacao BETWEEN ? AND ?
            ORDER BY m.data_movimentacao
        ";

        $rows = DB::select($sql, [
            $params['id_empresa'],
            $params['id_filial'],
            $params['id_produto'],
            $params['data_inicio'],
            $params['data_fim'],
        ]);

        return response()->json([
            'success' => true,
            'params' => $params,
            'data' => $rows,
        ]);
    }

    public function kardexResumo(Request $request)
    {
        $params = $this->resolveParametros($request);

        $sql = "
            SELECT
                p.descricao AS produto,
                p.codigo_barras,
                p.unidade_medida,
                e.nome_empresa,
                f.nome_filial,
                ROUND((
                    SELECT COALESCE(SUM(
                        CASE
                            WHEN tipo_movimentacao IN ('entrada','ajuste') THEN quantidade
                            WHEN tipo_movimentacao IN ('saida','transferencia') THEN -quantidade
                            ELSE 0
                        END
                    ), 0)
                    FROM tb_movimentacoes
                    WHERE id_empresa = ?
                      AND id_filial = ?
                      AND id_produto = ?
                      AND data_movimentacao < ?
                ), 2) AS saldo_inicial,
                ROUND(COALESCE((
                    SELECT SUM(quantidade)
                    FROM tb_movimentacoes
                    WHERE id_empresa = ?
                      AND id_filial = ?
                      AND id_produto = ?
                      AND tipo_movimentacao IN ('entrada','ajuste')
                      AND data_movimentacao BETWEEN ? AND ?
                ), 0), 2) AS total_entradas,
                ROUND(COALESCE((
                    SELECT SUM(quantidade)
                    FROM tb_movimentacoes
                    WHERE id_empresa = ?
                      AND id_filial = ?
                      AND id_produto = ?
                      AND tipo_movimentacao IN ('saida','transferencia')
                      AND data_movimentacao BETWEEN ? AND ?
                ), 0), 2) AS total_saidas,
                ROUND(COALESCE((
                    SELECT saldo_atual
                    FROM tb_movimentacoes
                    WHERE id_empresa = ?
                      AND id_filial = ?
                      AND id_produto = ?
                    ORDER BY data_movimentacao DESC
                    LIMIT 1
                ), 0), 2) AS saldo_atual,
                ROUND(es.pendencia_compra, 2) AS pendente_receber,
                ROUND(es.quantidade_reservada, 2) AS reservado,
                ROUND(es.quantidade + es.pendencia_compra, 2) AS estoque_futuro,
                DATEDIFF(?, ?) + 1 AS dias_no_periodo,
                ROUND(COALESCE((
                    SELECT AVG(custo_unitario)
                    FROM tb_movimentacoes
                    WHERE id_empresa = ?
                      AND id_filial = ?
                      AND id_produto = ?
                      AND custo_unitario > 0
                ), 0), 2) AS custo_medio
            FROM tb_produtos p
            CROSS JOIN tb_empresas e
            CROSS JOIN tb_filiais f
            LEFT JOIN tb_estoque es ON es.id_empresa = e.id_empresa
                AND es.id_filial = f.id_filial
                AND es.id_produto = p.id_produto
            WHERE p.id_produto = ?
              AND e.id_empresa = ?
              AND f.id_filial = ?
        ";

        $bindings = [
            $params['id_empresa'],
            $params['id_filial'],
            $params['id_produto'],
            $params['data_inicio'],

            $params['id_empresa'],
            $params['id_filial'],
            $params['id_produto'],
            $params['data_inicio'],
            $params['data_fim'],

            $params['id_empresa'],
            $params['id_filial'],
            $params['id_produto'],
            $params['data_inicio'],
            $params['data_fim'],

            $params['id_empresa'],
            $params['id_filial'],
            $params['id_produto'],

            $params['data_fim'],
            $params['data_inicio'],

            $params['id_empresa'],
            $params['id_filial'],
            $params['id_produto'],

            $params['id_produto'],
            $params['id_empresa'],
            $params['id_filial'],
        ];

        $row = DB::selectOne($sql, $bindings);

        return response()->json([
            'success' => true,
            'params' => $params,
            'data' => $row,
        ]);
    }
}
