<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventario\StoreTarefaProdutoRequest;
use App\Http\Requests\Inventario\UpdateTarefaProdutoRequest;
use App\Models\TarefaContagem;
use App\Models\TarefaContagemProduto;
use App\Models\Usuario;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TarefaContagemProdutoController extends Controller
{
    private const PERFIS_GERENCIA = ['super_admin', 'admin_empresa', 'manager'];

    private function usuarioAutenticado(): ?Usuario
    {
        $user = auth()->user();
        return $user instanceof Usuario ? $user : null;
    }

    private function podeGerenciar(Usuario $usuario): bool
    {
        return in_array($usuario->perfil, self::PERFIS_GERENCIA, true);
    }

    private function validaAcessoTarefa(Usuario $usuario, TarefaContagem $tarefa): ?\Illuminate\Http\JsonResponse
    {
        $capa = $tarefa->capaInventario;
        if ($usuario->perfil !== 'super_admin' && $capa && (int) $capa->id_empresa !== (int) $usuario->id_empresa) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado para este inventário.'
            ], Response::HTTP_FORBIDDEN);
        }

        if ($this->podeGerenciar($usuario)) {
            return null;
        }

        if ((int) $tarefa->id_usuario === (int) $usuario->id_usuario) {
            return null;
        }

        if ($tarefa->id_supervisor && (int) $tarefa->id_supervisor === (int) $usuario->id_usuario) {
            return null;
        }

        return response()->json([
            'success' => false,
            'message' => 'Você não tem permissão para acessar esta tarefa.'
        ], Response::HTTP_FORBIDDEN);
    }

    public function index($id_tarefa)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            $tarefa = TarefaContagem::with('capaInventario')->findOrFail($id_tarefa);
            $erroAcesso = $this->validaAcessoTarefa($usuario, $tarefa);
            if ($erroAcesso) {
                return $erroAcesso;
            }

            $produtos = TarefaContagemProduto::with('produto')
                ->where('id_tarefa', $id_tarefa)
                ->orderBy('id_produto')
                ->get()
                ->map(function ($item) {
                    return [
                        'id_produto' => $item->id_produto,
                        'descricao' => $item->produto?->descricao,
                        'quantidade_contada' => $item->quantidade_contada,
                        'data_contagem' => $item->data_contagem,
                        'observacao' => $item->observacao,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $produtos
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao listar produtos da tarefa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar produtos da tarefa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreTarefaProdutoRequest $request, $id_tarefa)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            $tarefa = TarefaContagem::with('capaInventario')->findOrFail($id_tarefa);
            $erroAcesso = $this->validaAcessoTarefa($usuario, $tarefa);
            if ($erroAcesso) {
                return $erroAcesso;
            }

            if (!$this->podeGerenciar($usuario)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para adicionar produtos.'
                ], Response::HTTP_FORBIDDEN);
            }
            $produtosInventario = DB::table('tb_inventario')
                ->where('id_capa_inventario', $tarefa->capaInventario?->id_capa_inventario)
                ->whereIn('id_produto', $request->produtos)
                ->pluck('id_produto')
                ->all();

            $solicitados = array_values(array_unique($request->produtos));
            $faltando = array_diff($solicitados, $produtosInventario);

            if (!empty($faltando)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Um ou mais produtos nao pertencem ao inventario informado.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $existentes = TarefaContagemProduto::where('id_tarefa', $id_tarefa)
                ->pluck('id_produto')
                ->all();

            $novos = array_diff($request->produtos, $existentes);
            $dataInsert = [];
            foreach ($novos as $produtoId) {
                $dataInsert[] = [
                    'id_tarefa' => $id_tarefa,
                    'id_produto' => $produtoId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($dataInsert)) {
                TarefaContagemProduto::insert($dataInsert);
            }

            return response()->json([
                'success' => true,
                'message' => 'Produtos adicionados com sucesso.',
                'data' => [
                    'produtos_adicionados' => count($dataInsert)
                ]
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Erro ao adicionar produtos na tarefa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar produtos na tarefa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateTarefaProdutoRequest $request, $id_tarefa, $id_produto)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            $tarefa = TarefaContagem::with('capaInventario')->findOrFail($id_tarefa);
            $erroAcesso = $this->validaAcessoTarefa($usuario, $tarefa);
            if ($erroAcesso) {
                return $erroAcesso;
            }

            $registro = TarefaContagemProduto::where('id_tarefa', $id_tarefa)
                ->where('id_produto', $id_produto)
                ->first();

            if (!$registro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produto não encontrado na tarefa.'
                ], Response::HTTP_NOT_FOUND);
            }

            $registro->quantidade_contada = $request->quantidade_contada;
            $registro->observacao = $request->observacao;
            $registro->data_contagem = now();
            $registro->save();

            return response()->json([
                'success' => true,
                'message' => 'Contagem registrada com sucesso.',
                'data' => [
                    'id_tarefa' => $id_tarefa,
                    'id_produto' => $id_produto,
                    'quantidade_contada' => $registro->quantidade_contada
                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar produto da tarefa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar produto da tarefa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id_tarefa, $id_produto)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            $tarefa = TarefaContagem::with('capaInventario')->findOrFail($id_tarefa);
            $erroAcesso = $this->validaAcessoTarefa($usuario, $tarefa);
            if ($erroAcesso) {
                return $erroAcesso;
            }

            if (!$this->podeGerenciar($usuario)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para remover produtos.'
                ], Response::HTTP_FORBIDDEN);
            }

            $registro = TarefaContagemProduto::where('id_tarefa', $id_tarefa)
                ->where('id_produto', $id_produto)
                ->first();

            if (!$registro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produto não encontrado na tarefa.'
                ], Response::HTTP_NOT_FOUND);
            }

            $registro->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produto removido com sucesso.'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao remover produto da tarefa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover produto da tarefa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
