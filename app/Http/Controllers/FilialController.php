<?php

namespace App\Http\Controllers;

use App\Models\Filial;
use Illuminate\Http\Request;

class FilialController extends Controller
{
    // Listar todas as filiais de uma empresa
    public function filiaisPorEmpresa($id_empresa)
    {
        $filiais = Filial::with('empresa')->where('id_empresa', $id_empresa)->get();
        return response()->json($filiais);
    }
    // Listar todas as filiais
    public function index()
    {
        $filiais = Filial::with('empresa')->get();
        return response()->json($filiais);
    }

    // Criar nova filial
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_empresa'    => 'required|exists:tb_empresas,id_empresa',
            'nome_filial'   => 'required|string|max:150',
            'endereco'      => 'nullable|string|max:255',
            'cidade'        => 'nullable|string|max:100',
            'estado'        => 'nullable|string|max:50',
            'cep'           => 'nullable|string|max:20',
            'data_cadastro' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        if (empty($validated['data_cadastro'])) {
            $validated['data_cadastro'] = now()->format('Y-m-d H:i:s');
        }

        $filial = Filial::create($validated);
        return response()->json($filial->load('empresa'), 201);
    }

    // Consultar filial por ID
    public function show($id)
    {
        $filial = Filial::with('empresa')->find($id);
        if (!$filial) {
            return response()->json(['message' => 'Filial não encontrada'], 404);
        }
        return response()->json($filial);
    }

    // Atualizar filial por ID
    public function update(Request $request, $id)
    {
        $filial = Filial::find($id);
        if (!$filial) {
            return response()->json(['message' => 'Filial não encontrada'], 404);
        }
        $validated = $request->validate([
            'nome_filial'   => 'sometimes|string|max:150',
            'endereco'      => 'nullable|string|max:255',
            'cidade'        => 'nullable|string|max:100',
            'estado'        => 'nullable|string|max:50',
            'cep'           => 'nullable|string|max:20',
            'data_cadastro' => 'nullable|date_format:Y-m-d H:i:s',
        ]);
        $filial->update($validated);
        return response()->json($filial->load('empresa'));
    }

    // Deletar filial por ID
    public function destroy($id)
    {
        $filial = Filial::find($id);
        if (!$filial) {
            return response()->json(['message' => 'Filial não encontrada'], 404);
        }
        $filial->delete();
        return response()->json(null, 204);
    }
}
