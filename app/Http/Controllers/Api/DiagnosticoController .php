<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiagnosticoController extends Controller
{
    public function verificarDados(Request $request)
    {
        try {
            $diagnostico = [];

            // 1. Verifica se existem registros na tabela principal
            $diagnostico['total_notas'] = DB::table('tb_nfe_cabecalho')->count();
            
            // 2. Verifica registros por empresa
            $diagnostico['notas_por_empresa'] = DB::table('tb_nfe_cabecalho')
                ->groupBy('id_empresa')
                ->select('id_empresa', DB::raw('COUNT(*) as total'))
                ->get();

            // 3. Verifica registros por status
            $diagnostico['notas_por_status'] = DB::table('tb_nfe_cabecalho')
                ->groupBy('status')
                ->select('status', DB::raw('COUNT(*) as total'))
                ->get();

            // 4. Verifica se existem registros com emitentes
            $diagnostico['notas_com_emitente'] = DB::table('tb_nfe_cabecalho as c')
                ->join('tb_nfe_emitente as e', 'c.id_nfe', '=', 'e.id_nfe')
                ->count();

            // 5. Verifica alguns registros de exemplo
            $diagnostico['exemplo_registros'] = DB::table('tb_nfe_cabecalho as c')
                ->leftJoin('tb_nfe_emitente as e', 'c.id_nfe', '=', 'e.id_nfe')
                ->select('c.id_nfe', 'c.id_empresa', 'c.status', 'c.nNF', 'c.dhEmi', 'e.CNPJ as emitente_cnpj')
                ->orderBy('c.id_nfe', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'diagnostico' => $diagnostico,
                'sugestoes' => [
                    'Verifique se os dados foram importados corretamente',
                    'Confirme o id_empresa correto',
                    'Verifique o status das notas (importada, validada, cancelada)',
                    'Confirme se existem registros nas tabelas relacionadas'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro no diagnóstico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function testarFiltros(Request $request)
    {
        try {
            $testes = [];

            // Teste 1: Filtro por empresa
            $testes['filtro_empresa_1'] = DB::table('tb_nfe_cabecalho')
                ->where('id_empresa', 1)
                ->count();

            // Teste 2: Filtro por status
            $testes['filtro_status_importada'] = DB::table('tb_nfe_cabecalho')
                ->where('status', 'importada')
                ->count();

            // Teste 3: Filtro combinado
            $testes['filtro_empresa_1_status_importada'] = DB::table('tb_nfe_cabecalho')
                ->where('id_empresa', 1)
                ->where('status', 'importada')
                ->count();

            // Teste 4: Verificar dados de emitentes
            $testes['emitentes_existentes'] = DB::table('tb_nfe_emitente')
                ->select('CNPJ', DB::raw('COUNT(*) as total'))
                ->groupBy('CNPJ')
                ->get();

            return response()->json([
                'success' => true,
                'testes' => $testes,
                'interpretacao' => [
                    'Se filtro_empresa_1_status_importada for 0, não há notas da empresa 1 com status importada',
                    'Verifique se os CNPJs dos emitentes correspondem ao filtro aplicado'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar filtros',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}