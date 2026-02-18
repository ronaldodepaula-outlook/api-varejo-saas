<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NfeCobranca;

class NfeCobrancaController extends Controller
{
    public function index(Request $request)
    {
        $id_empresa = $request->header('X-ID-EMPRESA');
        $cobrancas = NfeCobranca::with('duplicatas')
            ->whereHas('nfeCabecalho', function($q) use($id_empresa){
                $q->where('id_empresa', $id_empresa);
            })->get();

        return response()->json($cobrancas);
    }

    public function show($id)
    {
        $cobranca = NfeCobranca::with('duplicatas')->findOrFail($id);
        return response()->json($cobranca);
    }

    public function store(Request $request)
    {
        $cobranca = NfeCobranca::create($request->all());
        return response()->json($cobranca, 201);
    }

    public function update(Request $request, $id)
    {
        $cobranca = NfeCobranca::findOrFail($id);
        $cobranca->update($request->all());
        return response()->json($cobranca);
    }

    public function destroy($id)
    {
        $cobranca = NfeCobranca::findOrFail($id);
        $cobranca->delete();
        return response()->json(null, 204);
    }
}
