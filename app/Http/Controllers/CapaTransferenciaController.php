<?php

namespace App\Http\Controllers;

use App\Models\CapaTransferencia;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CapaTransferenciaController extends Controller
{
    public function index()
    {
        $capaTransferencias = CapaTransferencia::with(['empresa', 'filialOrigem', 'filialDestino', 'usuario', 'transferencias'])->get();
        return response()->json($capaTransferencias);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_empresa' => 'required|exists:tb_empresas,id_empresa',
            'id_filial_origem' => 'required|exists:tb_filiais,id_filial',
            'id_filial_destino' => 'required|exists:tb_filiais,id_filial',
            'data_transferencia' => 'required|date',
            'status' => 'required|in:pendente,enviada,recebida,cancelada',
            'observacao' => 'nullable|string',
            'id_usuario' => 'required|exists:tb_usuarios,id_usuario'
        ]);

        $capaTransferencia = CapaTransferencia::create($request->all());
        return response()->json($capaTransferencia, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $capaTransferencia = CapaTransferencia::with(['empresa', 'filialOrigem', 'filialDestino', 'usuario', 'transferencias'])->findOrFail($id);
        return response()->json($capaTransferencia);
    }

    public function update(Request $request, $id)
    {
        $capaTransferencia = CapaTransferencia::findOrFail($id);
        
        $request->validate([
            'id_empresa' => 'sometimes|required|exists:tb_empresas,id_empresa',
            'id_filial_origem' => 'sometimes|required|exists:tb_filiais,id_filial',
            'id_filial_destino' => 'sometimes|required|exists:tb_filiais,id_filial',
            'data_transferencia' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:pendente,enviada,recebida,cancelada',
            'observacao' => 'nullable|string',
            'id_usuario' => 'sometimes|required|exists:tb_usuarios,id_usuario'
        ]);

        $capaTransferencia->update($request->all());
        return response()->json($capaTransferencia);
    }

    public function destroy($id)
    {
        $capaTransferencia = CapaTransferencia::findOrFail($id);
        $capaTransferencia->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}