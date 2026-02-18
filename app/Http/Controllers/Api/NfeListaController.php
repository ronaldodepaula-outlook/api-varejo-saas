<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NfeListaController extends Controller
{
    public function index(Request $request)
    {
        try {
            \Log::info('=== INICIANDO CONSULTA NFE LISTA ===', $request->all());

            // Query base com joins
            $query = DB::table('tb_nfe_cabecalho as cab')
                ->leftJoin('tb_nfe_emitente as emit', 'cab.id_nfe', '=', 'emit.id_nfe')
                ->leftJoin('tb_nfe_destinatario as dest', 'cab.id_nfe', '=', 'dest.id_nfe')
                ->select(
                    // Campos do cabeçalho
                    'cab.id_nfe',
                    'cab.id_empresa',
                    'cab.id_filial',
                    'cab.cUF',
                    'cab.cNF',
                    'cab.natOp',
                    'cab.mods',
                    'cab.serie',
                    'cab.nNF',
                    'cab.dhEmi',
                    'cab.tpNF',
                    'cab.idDest',
                    'cab.cMunFG',
                    'cab.tpImp',
                    'cab.tpEmis',
                    'cab.cDV',
                    'cab.tpAmb',
                    'cab.finNFe',
                    'cab.indFinal',
                    'cab.indPres',
                    'cab.procEmi',
                    'cab.verProc',
                    'cab.valor_total',
                    'cab.chave_acesso',
                    'cab.status',
                    'cab.criado_em',
                    'cab.atualizado_em',
                    
                    // Campos do emitente
                    'emit.CNPJ as emitente_cnpj',
                    'emit.xNome as emitente_razao_social',
                    'emit.xFant as emitente_nome_fantasia',
                    'emit.IE as emitente_ie',
                    'emit.CRT as emitente_crt',
                    'emit.xLgr as emitente_logradouro',
                    'emit.nro as emitente_numero',
                    'emit.xBairro as emitente_bairro',
                    'emit.xMun as emitente_cidade',
                    'emit.UF as emitente_uf',
                    'emit.CEP as emitente_cep',
                    'emit.fone as emitente_telefone',
                    
                    // Campos do destinatário
                    'dest.CNPJ as destinatario_cnpj',
                    'dest.xNome as destinatario_razao_social',
                    'dest.IE as destinatario_ie',
                    'dest.email as destinatario_email',
                    'dest.xLgr as destinatario_logradouro',
                    'dest.nro as destinatario_numero',
                    'dest.xBairro as destinatario_bairro',
                    'dest.xMun as destinatario_cidade',
                    'dest.UF as destinatario_uf',
                    'dest.CEP as destinatario_cep',
                    'dest.fone as destinatario_telefone'
                );

            // Aplicar filtros
            if ($request->has('id_empresa') && !empty($request->id_empresa)) {
                $query->where('cab.id_empresa', $request->id_empresa);
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('cab.status', $request->status);
            }

            if ($request->has('data_inicio') && $request->has('data_fim')) {
                $query->whereBetween('cab.dhEmi', [$request->data_inicio, $request->data_fim]);
            }

            if ($request->has('cnpj_emitente') && !empty($request->cnpj_emitente)) {
                $cnpj = preg_replace('/[^0-9]/', '', $request->cnpj_emitente);
                $query->where('emit.CNPJ', $cnpj);
            }

            if ($request->has('nNF') && !empty($request->nNF)) {
                $query->where('cab.nNF', $request->nNF);
            }

            if ($request->has('chave_acesso') && !empty($request->chave_acesso)) {
                $query->where('cab.chave_acesso', 'like', '%' . $request->chave_acesso . '%');
            }

            // Ordenação padrão por data de emissão decrescente
            $sortField = $request->get('sort_field', 'cab.dhEmi');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            // Paginação
            $perPage = $request->get('per_page', 20);
            $notas = $query->paginate($perPage);

            // Adicionar quantidade de itens para cada nota
            foreach ($notas->items() as $nota) {
                $nota->quantidade_itens = DB::table('tb_nfe_itens')
                    ->where('id_nfe', $nota->id_nfe)
                    ->count();
                    
                $nota->valor_total_formatado = 'R$ ' . number_format($nota->valor_total, 2, ',', '.');
                $nota->data_emissao_formatada = date('d/m/Y H:i', strtotime($nota->dhEmi));
            }

            return response()->json([
                'success' => true,
                'message' => 'Notas fiscais listadas com sucesso',
                'data' => $notas->items(),
                'pagination' => [
                    'total' => $notas->total(),
                    'per_page' => $notas->perPage(),
                    'current_page' => $notas->currentPage(),
                    'last_page' => $notas->lastPage(),
                    'from' => $notas->firstItem(),
                    'to' => $notas->lastItem()
                ],
                'filtros_aplicados' => [
                    'id_empresa' => $request->id_empresa,
                    'status' => $request->status,
                    'data_inicio' => $request->data_inicio,
                    'data_fim' => $request->data_fim,
                    'cnpj_emitente' => $request->cnpj_emitente
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao listar notas fiscais', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar notas fiscais',
                'error' => $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Estatísticas das notas fiscais
     */
    public function estatisticas(Request $request)
    {
        try {
            $query = DB::table('tb_nfe_cabecalho');

            if ($request->has('id_empresa')) {
                $query->where('id_empresa', $request->id_empresa);
            }

            $estatisticas = [
                'total_notas' => $query->count(),
                'total_valor' => $query->sum('valor_total'),
                'valor_medio' => $query->avg('valor_total'),
                'por_status' => DB::table('tb_nfe_cabecalho')
                    ->when($request->has('id_empresa'), function ($q) use ($request) {
                        return $q->where('id_empresa', $request->id_empresa);
                    })
                    ->groupBy('status')
                    ->select('status', DB::raw('COUNT(*) as total, SUM(valor_total) as valor_total'))
                    ->get(),
                'ultimos_30_dias' => DB::table('tb_nfe_cabecalho')
                    ->when($request->has('id_empresa'), function ($q) use ($request) {
                        return $q->where('id_empresa', $request->id_empresa);
                    })
                    ->where('dhEmi', '>=', now()->subDays(30))
                    ->count(),
                'top_emitentes' => DB::table('tb_nfe_cabecalho as cab')
                    ->join('tb_nfe_emitente as emit', 'cab.id_nfe', '=', 'emit.id_nfe')
                    ->when($request->has('id_empresa'), function ($q) use ($request) {
                        return $q->where('cab.id_empresa', $request->id_empresa);
                    })
                    ->groupBy('emit.CNPJ', 'emit.xNome')
                    ->select(
                        'emit.CNPJ',
                        'emit.xNome as razao_social',
                        DB::raw('COUNT(*) as total_notas'),
                        DB::raw('SUM(cab.valor_total) as valor_total')
                    )
                    ->orderBy('valor_total', 'desc')
                    ->limit(10)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $estatisticas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar estatísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}