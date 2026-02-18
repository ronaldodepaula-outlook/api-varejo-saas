<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NfeImpostos;

class NfeImpostosController extends Controller
{
    public function index(Request $request)
    {
        $id_empresa = $request->header('X-ID-EMPRESA');
        $impostos = NfeImpostos::whereHas('item.nfeCabecalho', function($q) use($id_empresa){
            $q->where('id_empresa', $id_empresa);
        })->get();

        return response()->json($impostos);
    }

    public function show($id)
    {
        $imposto = NfeImpostos::findOrFail($id);
        return response()->json($imposto);
    }

    public function store(Request $request)
    {
        $imposto = NfeImpostos::create($request->all());
        return response()->json($imposto, 201);
    }

    public function update(Request $request, $id)
    {
        $imposto = NfeImpostos::findOrFail($id);
        $imposto->update($request->all());
        return response()->json($imposto);
    }

    public function destroy($id)
    {
        $imposto = NfeImpostos::findOrFail($id);
        $imposto->delete();
        return response()->json(null, 204);
    }
}
