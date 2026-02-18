<?php

namespace App\Http\Controllers;

use App\Models\Transferencia;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransferenciaController extends Controller
{
    public function index()
    {
        $transferencias = Transferencia::with(['empresa', 'filialOrigem', 'filialDestino', 'produto'])->get();
        return response()->json($transferencias);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_empresa' => 'required|exists:tb_empresas,id_empresa',
            'id_filial_origem' => 'required|exists:tb_filiais,id_filial',
            'id_filial_destino' => 'required|exists:tb_filiais,id_filial',
            'id_produto' => 'required|exists:tb_produtos,id_produto',
            'quantidade' => 'required|numeric',
            'status' => 'required|in:pendente,concluida,cancelada',
            'observacao' => 'nullable|string'
        ]);

        $transferencia = Transferencia::create($request->all());
        return response()->json($transferencia, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $transferencia = Transferencia::with(['empresa', 'filialOrigem', 'filialDestino', 'produto'])->findOrFail($id);
        return response()->json($transferencia);
    }

    public function update(Request $request, $id)
    {
        $transferencia = Transferencia::findOrFail($id);
        
        $request->validate([
            'id_empresa' => 'sometimes|required|exists:tb_empresas,id_empresa',
            'id_filial_origem' => 'sometimes|required|exists:tb_filiais,id_filial',
            'id_filial_destino' => 'sometimes|required|exists:tb_filiais,id_filial',
            'id_produto' => 'sometimes|required|exists:tb_produtos,id_produto',
            'quantidade' => 'sometimes|required|numeric',
            'status' => 'sometimes|required|in:pendente,concluida,cancelada',
            'observacao' => 'nullable|string'
        ]);

        $transferencia->update($request->all());
        return response()->json($transferencia);
    }

    public function destroy($id)
    {
        $transferencia = Transferencia::findOrFail($id);
        $transferencia->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}