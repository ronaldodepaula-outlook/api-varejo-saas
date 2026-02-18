<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NfeItens;

class NfeItensController extends Controller
{
    public function index(Request $request)
    {
        $id_empresa = $request->header('X-ID-EMPRESA');
        $itens = NfeItens::with('impostos')
            ->whereHas('nfeCabecalho', function($q) use($id_empresa){
                $q->where('id_empresa', $id_empresa);
            })->get();

        return response()->json($itens);
    }

    public function show($id)
    {
        $item = NfeItens::with('impostos')->findOrFail($id);
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $item = NfeItens::create($request->all());
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = NfeItens::findOrFail($id);
        $item->update($request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = NfeItens::findOrFail($id);
        $item->delete();
        return response()->json(null, 204);
    }
}
