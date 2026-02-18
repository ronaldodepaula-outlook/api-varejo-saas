<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebugController extends Controller
{
    public function testarJoin(Request $request)
    {
        try {
            // Teste 1: Join simples
            $teste1 = DB::table('tb_nfe_cabecalho as cab')
                ->join('tb_nfe_emitente as emit', 'cab.id_nfe', '=', 'emit.id_nfe')
                ->select('cab.id_nfe', 'cab.id_empresa', 'cab.status', 'emit.CNPJ')
                ->where('cab.id_empresa', 1)
                ->limit(3)
                ->get();

            // Teste 2: Verificar estrutura das tabelas
            $estrutura = [];
            $estrutura['tb_nfe_cabecalho'] = DB::select("DESCRIBE tb_nfe_cabecalho");
            $estrutura['tb_nfe_emitente'] = DB::select("DESCRIBE tb_nfe_emitente");

            return response()->json([
                'success' => true,
                'teste_join' => $teste1,
                'estrutura_tabelas' => $estrutura,
                'total_notas' => DB::table('tb_nfe_cabecalho')->count(),
                'total_emitentes' => DB::table('tb_nfe_emitente')->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}