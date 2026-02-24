<?php

namespace App\Http\Controllers;

use App\Models\SessaoAtiva;
use App\Models\Usuario;
use Illuminate\Http\Request;

class SessaoAtivaController extends Controller
{
    public function index(Request $request)
    {
        $query = SessaoAtiva::with('usuario');
        $usuario = auth()->user();
        if ($usuario && !$usuario->isAdmin()) {
            $query->where('id_usuario', $usuario->id_usuario);
        }

        if ($request->filled('id_usuario')) {
            $query->where('id_usuario', $request->get('id_usuario'));
        }
        if ($request->has('ativa')) {
            $query->where('ativa', (int) $request->get('ativa') === 1);
        }

        $query->orderByDesc('ultima_atividade');

        if ($request->filled('per_page')) {
            return response()->json($query->paginate((int) $request->get('per_page')));
        }

        return response()->json($query->get());
    }

    public function porUsuario($id_usuario, Request $request)
    {
        $usuario = auth()->user();
        if ($usuario && !$usuario->isAdmin() && (int) $id_usuario !== (int) $usuario->id_usuario) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }
        Usuario::findOrFail($id_usuario);
        $request->merge(['id_usuario' => $id_usuario]);
        return $this->index($request);
    }

    public function destroy($id)
    {
        $sessao = SessaoAtiva::findOrFail($id);
        $usuario = auth()->user();
        if ($usuario && !$usuario->isAdmin() && (int) $sessao->id_usuario !== (int) $usuario->id_usuario) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }
        $sessao->ativa = false;
        $sessao->save();

        return response()->json(['message' => 'Sessao encerrada']);
    }
}
