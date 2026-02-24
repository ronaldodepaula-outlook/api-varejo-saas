<?php

namespace App\Http\Controllers;

use App\Models\PerfilAcesso;
use App\Models\PermissaoPerfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerfilAcessoController extends Controller
{
    public function index(Request $request)
    {
        $query = PerfilAcesso::query();

        if ($request->boolean('incluir_excluidos')) {
            $query->withTrashed();
        }

        $usuario = auth()->user();
        if ($usuario && !$usuario->isAdmin()) {
            $query->where('nivel', '<', $usuario->nivelMaximo());
        }

        $perfis = $query->withCount('usuarios')
            ->orderBy('nivel', 'desc')
            ->orderBy('nome_perfil')
            ->get();

        return response()->json($perfis);
    }

    public function show($id)
    {
        $perfil = PerfilAcesso::with(['permissoes.modulo', 'permissoes.acao'])->findOrFail($id);

        if (!$this->usuarioPodeGerenciarPerfil($perfil)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        return response()->json([
            'perfil' => $perfil,
            'permissoes' => $this->formatarPermissoes($perfil->permissoes),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome_perfil' => 'required|string|max:100|unique:tb_perfis_acesso,nome_perfil',
            'descricao' => 'nullable|string|max:255',
            'nivel' => 'nullable|integer|min:0|max:1000',
            'is_default' => 'nullable|boolean',
        ]);

        $nivel = isset($data['nivel']) ? (int) $data['nivel'] : 0;
        if (!$this->usuarioPodeDefinirNivel($nivel)) {
            return response()->json(['message' => 'Nivel acima do permitido para o usuario atual'], 403);
        }

        if (!empty($data['is_default'])) {
            PerfilAcesso::query()->update(['is_default' => false]);
        }

        $perfil = PerfilAcesso::create([
            'nome_perfil' => $data['nome_perfil'],
            'descricao' => $data['descricao'] ?? null,
            'nivel' => $nivel,
            'is_default' => $data['is_default'] ?? false,
            'is_system' => false,
        ]);

        return response()->json($perfil, 201);
    }

    public function update(Request $request, $id)
    {
        $perfil = PerfilAcesso::findOrFail($id);

        if ($perfil->is_system) {
            return response()->json(['message' => 'Perfil de sistema nao pode ser alterado'], 403);
        }

        if (!$this->usuarioPodeGerenciarPerfil($perfil)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $data = $request->validate([
            'nome_perfil' => 'sometimes|string|max:100|unique:tb_perfis_acesso,nome_perfil,' . $id . ',id_perfil',
            'descricao' => 'nullable|string|max:255',
            'nivel' => 'nullable|integer|min:0|max:1000',
            'is_default' => 'nullable|boolean',
        ]);

        if (isset($data['nivel']) && !$this->usuarioPodeDefinirNivel((int) $data['nivel'])) {
            return response()->json(['message' => 'Nivel acima do permitido para o usuario atual'], 403);
        }

        if (!empty($data['is_default'])) {
            PerfilAcesso::query()->update(['is_default' => false]);
        }

        $perfil->update($data);

        return response()->json($perfil);
    }

    public function destroy($id)
    {
        $perfil = PerfilAcesso::findOrFail($id);

        if ($perfil->is_system) {
            return response()->json(['message' => 'Perfil de sistema nao pode ser excluido'], 403);
        }

        if (!$this->usuarioPodeGerenciarPerfil($perfil)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        if ($perfil->usuarios()->count() > 0) {
            return response()->json(['message' => 'Perfil possui usuarios vinculados'], 422);
        }

        $perfil->delete();

        return response()->json(null, 204);
    }

    public function permissoes($id)
    {
        $perfil = PerfilAcesso::with(['permissoes.modulo', 'permissoes.acao'])->findOrFail($id);

        if (!$this->usuarioPodeGerenciarPerfil($perfil)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        return response()->json([
            'perfil' => $perfil,
            'permissoes' => $this->formatarPermissoes($perfil->permissoes),
        ]);
    }

    public function atualizarPermissoes(Request $request, $id)
    {
        $perfil = PerfilAcesso::findOrFail($id);

        if ($perfil->is_system) {
            return response()->json(['message' => 'Perfil de sistema nao pode ser alterado'], 403);
        }

        if (!$this->usuarioPodeGerenciarPerfil($perfil)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $data = $request->validate([
            'permissoes' => 'required|array|min:1',
            'permissoes.*.id_modulo' => 'required|exists:tb_modulos_sistema,id_modulo',
            'permissoes.*.id_acao' => 'required|exists:tb_permissoes_acoes,id_acao',
            'permissoes.*.permitido' => 'nullable|boolean',
        ]);

        $permissoes = collect($data['permissoes'])->unique(function ($item) {
            return $item['id_modulo'] . '-' . $item['id_acao'];
        });

        DB::transaction(function () use ($perfil, $permissoes) {
            PermissaoPerfil::where('id_perfil', $perfil->id_perfil)->delete();

            foreach ($permissoes as $permissao) {
                PermissaoPerfil::create([
                    'id_perfil' => $perfil->id_perfil,
                    'id_modulo' => $permissao['id_modulo'],
                    'id_acao' => $permissao['id_acao'],
                    'permitido' => isset($permissao['permitido']) ? (bool) $permissao['permitido'] : true,
                ]);
            }
        });

        $perfil->load(['permissoes.modulo', 'permissoes.acao']);

        return response()->json([
            'perfil' => $perfil,
            'permissoes' => $this->formatarPermissoes($perfil->permissoes),
        ]);
    }

    private function formatarPermissoes($permissoes)
    {
        return $permissoes->groupBy('id_modulo')->map(function ($itens) {
            $primeiro = $itens->first();
            return [
                'modulo' => [
                    'id_modulo' => $primeiro->modulo->id_modulo ?? null,
                    'nome_modulo' => $primeiro->modulo->nome_modulo ?? null,
                    'icone' => $primeiro->modulo->icone ?? null,
                    'rota' => $primeiro->modulo->rota ?? null,
                ],
                'acoes' => $itens->map(function ($p) {
                    return [
                        'id_acao' => $p->acao->id_acao ?? null,
                        'nome_acao' => $p->acao->nome_acao ?? null,
                        'codigo_acao' => $p->acao->codigo_acao ?? null,
                        'permitido' => (bool) $p->permitido,
                    ];
                })->values(),
            ];
        })->values();
    }

    private function usuarioPodeGerenciarPerfil(PerfilAcesso $perfil): bool
    {
        $usuario = auth()->user();
        if (!$usuario) {
            return true;
        }
        if ($usuario->isAdmin()) {
            return true;
        }
        return $perfil->nivel < $usuario->nivelMaximo();
    }

    private function usuarioPodeDefinirNivel(int $nivel): bool
    {
        $usuario = auth()->user();
        if (!$usuario) {
            return true;
        }
        if ($usuario->isAdmin()) {
            return true;
        }
        return $nivel < $usuario->nivelMaximo();
    }
}
