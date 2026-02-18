<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NfeTransporte;

class NfeTransporteController extends Controller
{
    public function index(Request $request)
    {
        $id_empresa = $request->header('X-ID-EMPRESA');
        $transportes = NfeTransporte::whereHas('nfeCabecalho', function($q) use($id_empresa){
            $q->where('id_empresa', $id_empresa);
        })->get();

        return response()->json($transportes);
    }

    public function show($id)
    {
        $transporte = NfeTransporte::findOrFail($id);
        return response()->json($transporte);
    }

    public function store(Request $request)
    {
        $transporte = NfeTransporte::create($request->all());
        return response()->json($transporte, 201);
    }

    public function update(Request $request, $id)
    {
        $transporte = NfeTransporte::findOrFail($id);
        $transporte->update($request->all());
        return response()->json($transporte);
    }

    public function destroy($id)
    {
        $transporte = NfeTransporte::findOrFail($id);
        $transporte->delete();
        return response()->json(null, 204);
    }
}
