<?php

namespace App\Http\Controllers;

use App\Models\LogAcaoUsuario;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAcaoUsuario::with(['usuario', 'empresa']);
        $usuario = auth()->user();
        if ($usuario && !$usuario->isAdmin()) {
            $query->where('id_empresa', $usuario->id_empresa);
        }

        if ($request->filled('id_usuario')) {
            $query->where('id_usuario', $request->get('id_usuario'));
        }
        if ($request->filled('id_empresa')) {
            $query->where('id_empresa', $request->get('id_empresa'));
        }
        if ($request->filled('acao')) {
            $query->where('acao', $request->get('acao'));
        }
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->get('modulo'));
        }
        if ($request->filled('tabela')) {
            $query->where('tabela', $request->get('tabela'));
        }
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->get('data_inicio'));
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->get('data_fim'));
        }

        $query->orderByDesc('created_at');

        if ($request->filled('per_page')) {
            return response()->json($query->paginate((int) $request->get('per_page')));
        }

        return response()->json($query->get());
    }

    public function porUsuario($id_usuario, Request $request)
    {
        $request->merge(['id_usuario' => $id_usuario]);
        return $this->index($request);
    }

    public function porModulo($modulo, Request $request)
    {
        $request->merge(['modulo' => $modulo]);
        return $this->index($request);
    }

    public function porData($data, Request $request)
    {
        $request->merge(['data_inicio' => $data, 'data_fim' => $data]);
        return $this->index($request);
    }
}
