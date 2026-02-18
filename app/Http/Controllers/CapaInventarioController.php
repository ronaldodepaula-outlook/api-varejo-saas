<?php

namespace App\Http\Controllers;

use App\Models\CapaInventario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CapaInventarioController extends Controller
{
    /**
     * Listar todas as capas de inventário com suas relações.
     */
    public function index()
    {
        try {
            $capas = CapaInventario::with(['empresa', 'filial', 'usuario', 'inventarios'])
                ->orderBy('data_inicio', 'desc')
                ->get();

            return response()->json($capas, Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao listar capas de inventário: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao listar inventários'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Criar nova capa de inventário.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_empresa'   => 'required|exists:tb_empresas,id_empresa',
            'id_filial'    => 'required|exists:tb_filiais,id_filial',
            'descricao'    => 'required|string|max:150',
            'data_inicio'  => 'required|date',
            'status'       => 'required|in:em_andamento,concluido,cancelado',
            'observacao'   => 'nullable|string',
            'id_usuario'   => 'required|exists:tb_usuarios,id_usuario'
        ]);

        try {
            $capa = CapaInventario::create($request->all());
            return response()->json($capa, Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Erro ao criar inventário: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao criar inventário'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Exibir uma capa específica.
     */
    public function show($id)
    {
        try {
            $capa = CapaInventario::with(['empresa', 'filial', 'usuario', 'inventarios'])
                ->findOrFail($id);

            return response()->json($capa, Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => 'Inventário não encontrado'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Atualizar uma capa de inventário.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_empresa'     => 'sometimes|required|exists:tb_empresas,id_empresa',
            'id_filial'      => 'sometimes|required|exists:tb_filiais,id_filial',
            'descricao'      => 'sometimes|required|string|max:150',
            'data_inicio'    => 'sometimes|required|date',
            'data_fechamento'=> 'nullable|date',
            'status'         => 'sometimes|required|in:em_andamento,concluido,cancelado',
            'observacao'     => 'nullable|string',
            'id_usuario'     => 'sometimes|required|exists:tb_usuarios,id_usuario'
        ]);

        try {
            DB::beginTransaction();

            $capa = CapaInventario::findOrFail($id);
            $capa->fill($request->all());
            $capa->save();

            DB::commit();
            return response()->json($capa, Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar inventário: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao atualizar inventário'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Excluir uma capa de inventário.
     */
    public function destroy($id)
    {
        try {
            $capa = CapaInventario::findOrFail($id);
            $capa->delete();

            return response()->json(['message' => 'Inventário excluído com sucesso'], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao excluir inventário: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao excluir inventário'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Listar capas por empresa.
     */
    public function listarPorEmpresa($id_empresa)
    {
        try {
            $capas = CapaInventario::with(['filial', 'usuario', 'inventarios'])
                ->where('id_empresa', $id_empresa)
                ->orderBy('data_inicio', 'desc')
                ->get();

            return response()->json($capas, Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao listar inventários por empresa: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao listar inventários'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Finalizar inventário (sem conflito com triggers).
     */
    public function finalizar($id)
    {
        try {
            DB::beginTransaction();

            $capa = CapaInventario::findOrFail($id);

            // Evita conflito com triggers atualizando via campo seguro
            DB::table('tb_capa_inventario')
                ->where('id_capa_inventario', $id)
                ->update([
                    'status' => 'concluido',
                    'data_fechamento' => now(),
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json(['message' => 'Inventário finalizado com sucesso'], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao finalizar inventário: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao finalizar inventário'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
