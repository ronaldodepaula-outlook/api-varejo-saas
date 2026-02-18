<?php

namespace App\Http\Controllers\Vendas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class DebitoClienteController extends Controller
{
    /**
     * Listar todos os débitos com filtros opcionais
     */
    public function index(Request $request)
    {
        try {
            $query = DB::table('tb_debitos_clientes')
                ->select('*')
                ->orderBy('data_geracao', 'desc');

            if ($request->filled('id_empresa')) {
                $query->where('id_empresa', $request->id_empresa);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return response()->json($query->paginate(25));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Exibir débito específico
     */
    public function show($id)
    {
        try {
            $debito = DB::table('tb_debitos_clientes')->find($id);
            if (!$debito) {
                return response()->json(['message' => 'Débito não encontrado'], 404);
            }
            return response()->json($debito);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Atualizar débito (status e data_pagamento)
     */
    public function update(Request $request, $id)
    {
        try {
            $debito = DB::table('tb_debitos_clientes')->find($id);
            if (!$debito) {
                return response()->json(['message' => 'Débito não encontrado'], 404);
            }

            $data = [];
            if ($request->filled('status')) {
                $data['status'] = $request->status;
            }
            if ($request->filled('data_pagamento')) {
                $data['data_pagamento'] = $request->data_pagamento;
            }

            DB::table('tb_debitos_clientes')
                ->where('id', $id)
                ->update($data);

            return response()->json(['message' => 'Débito atualizado com sucesso']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Listar débitos por cliente
     */
    public function porCliente($id_cliente)
    {
        try {
            $debitos = DB::table('tb_debitos_clientes')
                ->where('id_cliente', $id_cliente)
                ->orderBy('data_geracao', 'desc')
                ->get();

            return response()->json($debitos);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Listar débitos por empresa
     */
    public function porEmpresa($id_empresa)
    {
        try {
            $debitos = DB::table('tb_debitos_clientes')
                ->where('id_empresa', $id_empresa)
                ->orderBy('data_geracao', 'desc')
                ->get();

            return response()->json($debitos);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Listar débitos por status
     */
    public function porStatus($status)
    {
        try {
            $debitos = DB::table('tb_debitos_clientes')
                ->where('status', $status)
                ->orderBy('data_geracao', 'desc')
                ->get();

            return response()->json($debitos);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}