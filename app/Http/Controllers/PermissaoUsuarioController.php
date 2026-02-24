<?php

namespace App\Http\Controllers;

use App\Models\PermissaoUsuario;
use App\Models\Usuario;
use Illuminate\Http\Request;

class PermissaoUsuarioController extends Controller
{
    public function index($id_usuario)
    {
        $usuario = Usuario::findOrFail($id_usuario);

        if (!$this->usuarioAtualPodeGerenciar($usuario)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $permissoes = PermissaoUsuario::with(['modulo', 'acao'])
            ->where('id_usuario', $usuario->id_usuario)
            ->orderBy('id_modulo')
            ->orderBy('id_acao')
            ->get();

        return response()->json($permissoes);
    }

    public function store(Request $request, $id_usuario)
    {
        $usuario = Usuario::findOrFail($id_usuario);

        if (!$this->usuarioAtualPodeGerenciar($usuario)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $data = $request->validate([
            'id_modulo' => 'required|exists:tb_modulos_sistema,id_modulo',
            'id_acao' => 'required|exists:tb_permissoes_acoes,id_acao',
            'permitido' => 'required|boolean',
        ]);

        $permissao = PermissaoUsuario::updateOrCreate(
            [
                'id_usuario' => $usuario->id_usuario,
                'id_modulo' => $data['id_modulo'],
                'id_acao' => $data['id_acao'],
            ],
            [
                'permitido' => (bool) $data['permitido'],
            ]
        );

        return response()->json($permissao, 201);
    }

    public function update(Request $request, $id_usuario, $id_permissao)
    {
        $usuario = Usuario::findOrFail($id_usuario);

        if (!$this->usuarioAtualPodeGerenciar($usuario)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $data = $request->validate([
            'permitido' => 'required|boolean',
        ]);

        $permissao = PermissaoUsuario::where('id_usuario', $usuario->id_usuario)
            ->where('id_permissao_usuario', $id_permissao)
            ->firstOrFail();

        $permissao->update(['permitido' => (bool) $data['permitido']]);

        return response()->json($permissao);
    }

    public function destroy($id_usuario, $id_permissao)
    {
        $usuario = Usuario::findOrFail($id_usuario);

        if (!$this->usuarioAtualPodeGerenciar($usuario)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $permissao = PermissaoUsuario::where('id_usuario', $usuario->id_usuario)
            ->where('id_permissao_usuario', $id_permissao)
            ->firstOrFail();

        $permissao->delete();

        return response()->json(null, 204);
    }

    private function usuarioAtualPodeGerenciar(Usuario $alvo): bool
    {
        $usuario = auth()->user();
        if (!$usuario) {
            return true;
        }
        if ($usuario->isAdmin()) {
            return true;
        }
        if ((int) $usuario->id_empresa !== (int) $alvo->id_empresa) {
            return false;
        }
        return $usuario->nivelMaximo() > $alvo->nivelMaximo();
    }
}
