<?php

namespace App\Http\Controllers;

use App\Models\NfeCabecalho;
use Illuminate\Http\Request;

class GestaoNfeController extends Controller
{
    /**
     * Listar NFes por empresa
     */
    public function listarPorEmpresa($id_empresa)
    {
        $notas = NfeCabecalho::with([
                'emitente',
                'destinatario',
                'itens',
                'transporte',
                'cobranca',
                'pagamentos',
                'informacoesAdicionais'
            ])
            ->where('id_empresa', $id_empresa)
            ->get();

        return response()->json($notas);
    }

    /**
     * Listar NFes por empresa e filial
     */
    public function listarPorEmpresaFilial($id_empresa, $id_filial)
    {
        $notas = NfeCabecalho::with([
                'emitente',
                'destinatario',
                'itens',
                'transporte',
                'cobranca',
                'pagamentos',
                'informacoesAdicionais'
            ])
            ->where('id_empresa', $id_empresa)
            ->where('id_filial', $id_filial)
            ->get();

        return response()->json($notas);
    }

    /**
     * Listar NFes por empresa e cNF
     */
    public function listarPorEmpresaECnf($id_empresa, $cNF)
    {
        $notas = NfeCabecalho::with([
                'emitente',
                'destinatario',
                'itens',
                'transporte',
                'cobranca',
                'pagamentos',
                'informacoesAdicionais'
            ])
            ->where('id_empresa', $id_empresa)
            ->where('cNF', $cNF)
            ->get();

        return response()->json($notas);
    }

    /**
     * Listar NFes por empresa e período
     */
    public function listarPorEmpresaEPeriodo(Request $request, $id_empresa)
    {
        $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio'
        ]);

        $notas = NfeCabecalho::with([
                'emitente',
                'destinatario',
                'itens',
                'transporte',
                'cobranca',
                'pagamentos',
                'informacoesAdicionais'
            ])
            ->where('id_empresa', $id_empresa)
            ->whereBetween('dhEmi', [
                $request->data_inicio,
                $request->data_fim
            ])
            ->get();

        return response()->json($notas);
    }

    /**
     * Listar NFes por emitente (CNPJ) - AGORA DEVE FUNCIONAR
     */
    public function listarPorEmitente($cnpj)
    {
        $notas = NfeCabecalho::with([
                'emitente',
                'destinatario',
                'itens',
                'transporte',
                'cobranca',
                'pagamentos',
                'informacoesAdicionais'
            ])
            ->whereHas('emitente', function($query) use ($cnpj) {
                $query->where('CNPJ', $cnpj);
            })
            ->get();

        return response()->json($notas);
    }

    /**
     * Listar NFes por destinatário (CNPJ) - AGORA DEVE FUNCIONAR
     */
    public function listarPorDestinatario($cnpj)
    {
        $notas = NfeCabecalho::with([
                'emitente',
                'destinatario',
                'itens',
                'transporte',
                'cobranca',
                'pagamentos',
                'informacoesAdicionais'
            ])
            ->whereHas('destinatario', function($query) use ($cnpj) {
                $query->where('CNPJ', $cnpj);
            })
            ->get();

        return response()->json($notas);
    }

    /**
     * Método adicional: Buscar NFe por chave de acesso
     */
    public function listarPorChaveAcesso($chave_acesso)
    {
        $nota = NfeCabecalho::with([
                'emitente',
                'destinatario',
                'itens',
                'transporte',
                'cobranca',
                'pagamentos',
                'informacoesAdicionais'
            ])
            ->where('chave_acesso', $chave_acesso)
            ->first();

        if (!$nota) {
            return response()->json(['message' => 'NFe não encontrada'], 404);
        }

        return response()->json($nota);
    }
}