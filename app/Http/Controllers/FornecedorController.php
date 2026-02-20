<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fornecedor;

class FornecedorController extends Controller
{
    // Listagem paginada, respeitando id_empresa do auth ou query
    public function index(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $fornecedores = Fornecedor::daEmpresa($empresaId)
            ->orderBy('razao_social')
            ->paginate(20);

        return response()->json($fornecedores);
    }

    // Listar todos sem paginação
    public function todosPorEmpresa(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $fornecedores = Fornecedor::daEmpresa($empresaId)
            ->orderBy('razao_social')
            ->get();

        return response()->json($fornecedores);
    }

    // Rota similar a outras: fornecedoresPorEmpresa($id_empresa)
    public function fornecedoresPorEmpresa($id_empresa)
    {
        $fornecedores = Fornecedor::daEmpresa($id_empresa)
            ->orderBy('razao_social')
            ->get();

        return response()->json($fornecedores);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa' => 'required|integer|exists:tb_empresas,id_empresa',
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:30',
            'inscricao_estadual' => 'nullable|string|max:50',
            'contato' => 'nullable|string|max:100',
            'telefone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:10',
            'cep' => 'nullable|string|max:20',
            'status' => 'nullable|string|max:50',
        ]);

        $fornecedor = Fornecedor::create($data);

        return response()->json(['success' => true, 'data' => $fornecedor], 201);
    }

    public function show(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $fornecedor = Fornecedor::daEmpresa($empresaId)->findOrFail($id);

        return response()->json($fornecedor);
    }

    public function update(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $fornecedor = Fornecedor::daEmpresa($empresaId)->findOrFail($id);

        $data = $request->validate([
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:30',
            'inscricao_estadual' => 'nullable|string|max:50',
            'contato' => 'nullable|string|max:100',
            'telefone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:10',
            'cep' => 'nullable|string|max:20',
            'status' => 'nullable|string|max:50',
        ]);

        $fornecedor->update($data);

        return response()->json(['success' => true, 'data' => $fornecedor]);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $fornecedor = Fornecedor::daEmpresa($empresaId)->findOrFail($id);
        $fornecedor->delete();

        return response()->json(['success' => true]);
    }
}
