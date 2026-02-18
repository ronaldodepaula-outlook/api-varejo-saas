<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::all();
        return response()->json($empresas);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|unique:tb_empresas,cnpj',
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string',
            'email' => 'nullable|email'
        ]);

        $empresa = Empresa::create($request->all());
        return response()->json($empresa, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $empresa = Empresa::findOrFail($id);
        return response()->json($empresa);
    }

    public function update(Request $request, $id)
    {
        $empresa = Empresa::findOrFail($id);
        
        $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'cnpj' => 'sometimes|required|string|unique:tb_empresas,cnpj,' . $id . ',id_empresa',
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string',
            'email' => 'nullable|email'
        ]);

        $empresa->update($request->all());
        return response()->json($empresa);
    }

    public function destroy($id)
    {
        $empresa = Empresa::findOrFail($id);
        $empresa->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}