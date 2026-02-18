<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NfeDetalhesController extends Controller
{
    public function show($id)
    {
        try {
            // Busca o cabeçalho da nota
            $cabecalho = DB::table('tb_nfe_cabecalho')->where('id_nfe', $id)->first();
            
            if (!$cabecalho) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nota fiscal não encontrada.'
                ], 404);
            }

            // Busca os dados do emitente
            $emitente = DB::table('tb_nfe_emitente')->where('id_nfe', $id)->first();

            // Busca os dados do destinatário
            $destinatario = DB::table('tb_nfe_destinatario')->where('id_nfe', $id)->first();

            // Busca os itens da nota
            $itens = DB::table('tb_nfe_itens as item')
                ->leftJoin('tb_nfe_impostos as imposto', 'item.id_item', '=', 'imposto.id_item')
                ->where('item.id_nfe', $id)
                ->select(
                    'item.*',
                    'imposto.vTotTrib',
                    'imposto.orig',
                    'imposto.CSOSN',
                    'imposto.CST',
                    'imposto.vBC',
                    'imposto.pIPI',
                    'imposto.vIPI',
                    'imposto.vPIS',
                    'imposto.vCOFINS'
                )
                ->orderBy('item.nItem')
                ->get();

            // Busca os dados de transporte
            $transporte = DB::table('tb_nfe_transporte')->where('id_nfe', $id)->first();

            // Busca os dados de cobrança
            $cobranca = DB::table('tb_nfe_cobranca')->where('id_nfe', $id)->first();

            // Busca as duplicatas (se existir cobrança)
            $duplicatas = [];
            if ($cobranca) {
                $duplicatas = DB::table('tb_nfe_duplicatas')
                    ->where('id_cobranca', $cobranca->id_cobranca)
                    ->orderBy('dVenc')
                    ->get();
            }

            // Busca os pagamentos
            $pagamentos = DB::table('tb_nfe_pagamentos')->where('id_nfe', $id)->get();

            // Busca as informações adicionais
            $informacoesAdicionais = DB::table('tb_nfe_informacoes_adicionais')
                ->where('id_nfe', $id)
                ->first();

            // Calcula totais dos itens
            $totaisItens = [
                'quantidade_itens' => $itens->count(),
                'valor_total_itens' => $itens->sum('vProd'),
                'quantidade_total' => $itens->sum('qCom'),
                'valor_total_impostos' => $itens->sum(function($item) {
                    return ($item->vIPI ?? 0) + ($item->vPIS ?? 0) + ($item->vCOFINS ?? 0);
                })
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'cabecalho' => $cabecalho,
                    'emitente' => $emitente,
                    'destinatario' => $destinatario,
                    'itens' => [
                        'dados' => $itens,
                        'totais' => $totaisItens
                    ],
                    'transporte' => $transporte,
                    'cobranca' => $cobranca,
                    'duplicatas' => $duplicatas,
                    'pagamentos' => $pagamentos,
                    'informacoes_adicionais' => $informacoesAdicionais
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar detalhes da nota fiscal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca itens específicos de uma nota com filtros
     */
    public function itens(Request $request, $id)
    {
        try {
            $query = DB::table('tb_nfe_itens as item')
                ->leftJoin('tb_nfe_impostos as imposto', 'item.id_item', '=', 'imposto.id_item')
                ->where('item.id_nfe', $id);

            // Filtros para itens
            if ($request->has('ncm')) {
                $query->where('item.NCM', $request->ncm);
            }

            if ($request->has('cfop')) {
                $query->where('item.CFOP', $request->cfop);
            }

            if ($request->has('codigo_produto')) {
                $query->where('item.cProd', 'like', '%' . $request->codigo_produto . '%');
            }

            if ($request->has('descricao_produto')) {
                $query->where('item.xProd', 'like', '%' . $request->descricao_produto . '%');
            }

            $itens = $query->select(
                    'item.*',
                    'imposto.vTotTrib',
                    'imposto.orig',
                    'imposto.CSOSN',
                    'imposto.CST',
                    'imposto.vBC',
                    'imposto.pIPI',
                    'imposto.vIPI',
                    'imposto.vPIS',
                    'imposto.vCOFINS'
                )
                ->orderBy('item.nItem')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $itens,
                'total_itens' => $itens->count(),
                'valor_total' => $itens->sum('vProd')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar itens da nota fiscal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}