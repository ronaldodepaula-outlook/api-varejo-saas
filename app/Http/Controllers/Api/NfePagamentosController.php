<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NfePagamentos;

class NfePagamentosController extends Controller
{
    public function index(Request $request)
    {
        $id_empresa = $request->header('X-ID-EMPRESA');
        $pagamentos = NfePagamentos::whereHas('nfeCabecalho', function($q) use($id_empresa){
            $q->where('id_empresa', $id_empresa);
        })->get();

        return response()->json($pagamentos);
    }

    public function show($id)
    {
        $pagamento = NfePagamentos::findOrFail($id);
        return response()->json($pagamento);
    }

    public function store(Request $request)
    {
        $pagamento = NfePagamentos::create($request->all());
        return response()->json($pagamento, 201);
    }

    public function update(Request $request, $id)
    {
        $pagamento = NfePagamentos::findOrFail($id);
        $pagamento->update($request->all());
        return response()->json($pagamento);
    }

    public function destroy($id)
    {
        $pagamento = NfePagamentos::findOrFail($id);
        $pagamento->delete();
        return response()->json(null, 204);
    }
}
