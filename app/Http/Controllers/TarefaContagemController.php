<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventario\CancelarTarefaRequest;
use App\Http\Requests\Inventario\ConcluirTarefaRequest;
use App\Http\Requests\Inventario\IniciarTarefaRequest;
use App\Http\Requests\Inventario\PausarTarefaRequest;
use App\Http\Requests\Inventario\RetomarTarefaRequest;
use App\Http\Requests\Inventario\StoreTarefaContagemRequest;
use App\Models\CapaInventario;
use App\Models\TarefaContagem;
use App\Models\TarefaContagemProduto;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TarefaContagemController extends Controller
{
    private const STATUS_VALIDOS = ['pendente', 'em_andamento', 'pausada', 'concluida', 'cancelada'];
    private const TIPOS_VALIDOS = ['contagem_inicial', 'recontagem', 'conferencia'];
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

    private function aplicaEscopoEmpresa($query, Usuario $usuario)
    {
        if ($usuario->perfil === 'super_admin') {
            return $query;
        }

        return $query->whereHas('capaInventario', function ($q) use ($usuario) {
            $q->where('id_empresa', $usuario->id_empresa);
        });
    }

    private function validaAcessoEmpresa(Usuario $usuario, CapaInventario $capa): ?\Illuminate\Http\JsonResponse
    {
        if ($usuario->perfil === 'super_admin') {
            return null;
        }

        if ((int) $capa->id_empresa !== (int) $usuario->id_empresa) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado para este inventário.'
            ], Response::HTTP_FORBIDDEN);
        }

        return null;
    }

    private function validaAcessoTarefa(Usuario $usuario, TarefaContagem $tarefa): ?\Illuminate\Http\JsonResponse
    {
        $capa = $tarefa->capaInventario;
        if ($capa) {
            $erroEmpresa = $this->validaAcessoEmpresa($usuario, $capa);
            if ($erroEmpresa) {
                return $erroEmpresa;
            }
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
            'message' => 'Você não tem permissão para gerenciar esta tarefa.'
        ], Response::HTTP_FORBIDDEN);
    }

    private function anexarObservacao(?string $observacoesAtuais, string $texto): string
    {
        $texto = trim($texto);
        if ($texto === '') {
            return $observacoesAtuais ?? '';
        }

        $linha = now()->format('Y-m-d H:i:s') . ' - ' . $texto;
        $partes = array_filter([
            $observacoesAtuais,
            $linha
        ]);

        return implode("\n", $partes);
    }

    private function mapResumo(TarefaContagem $tarefa): array
    {
        return [
            'id_tarefa' => $tarefa->id_tarefa,
            'id_capa_inventario' => $tarefa->id_capa_inventario,
            'inventario' => $tarefa->capaInventario ? [
                'id_capa_inventario' => $tarefa->capaInventario->id_capa_inventario,
                'descricao' => $tarefa->capaInventario->descricao,
                'status' => $tarefa->capaInventario->status,
            ] : null,
            'usuario' => $tarefa->usuario ? [
                'id_usuario' => $tarefa->usuario->id_usuario,
                'nome' => $tarefa->usuario->nome,
            ] : null,
            'supervisor' => $tarefa->supervisor ? [
                'id_usuario' => $tarefa->supervisor->id_usuario,
                'nome' => $tarefa->supervisor->nome,
            ] : null,
            'tipo_tarefa' => $tarefa->tipo_tarefa,
            'status' => $tarefa->status,
            'data_inicio' => $tarefa->data_inicio,
            'data_fim' => $tarefa->data_fim,
            'observacoes' => $tarefa->observacoes,
            'created_at' => $tarefa->created_at,
            'updated_at' => $tarefa->updated_at,
            'total_produtos' => $tarefa->total_produtos ?? 0,
            'produtos_contados' => $tarefa->produtos_contados ?? 0,
        ];
    }

    public function index(Request $request)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            if ($request->filled('status') && !in_array($request->status, self::STATUS_VALIDOS, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status inválido para filtro.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $query = TarefaContagem::query()
                ->with(['capaInventario', 'usuario', 'supervisor'])
                ->withCount([
                    'produtos as total_produtos',
                    'produtos as produtos_contados' => function ($q) {
                        $q->whereNotNull('quantidade_contada');
                    }
                ])
                ->orderBy('created_at', 'desc');

            $this->aplicaEscopoEmpresa($query, $usuario);

            if (!$this->podeGerenciar($usuario)) {
                $query->where('id_usuario', $usuario->id_usuario);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('id_capa_inventario')) {
                $query->where('id_capa_inventario', $request->id_capa_inventario);
            }

            if ($request->filled('id_usuario')) {
                $query->where('id_usuario', $request->id_usuario);
            }

            $dataInicio = $request->input('data_inicio');
            $dataFim = $request->input('data_fim');
            if ($dataInicio || $dataFim) {
                $query->where(function ($q) use ($dataInicio, $dataFim) {
                    $q->where(function ($q1) use ($dataInicio, $dataFim) {
                        if ($dataInicio) {
                            $q1->whereDate('data_inicio', '>=', $dataInicio);
                        }
                        if ($dataFim) {
                            $q1->whereDate('data_inicio', '<=', $dataFim);
                        }
                    })->orWhere(function ($q2) use ($dataInicio, $dataFim) {
                        $q2->whereNull('data_inicio');
                        if ($dataInicio) {
                            $q2->whereDate('created_at', '>=', $dataInicio);
                        }
                        if ($dataFim) {
                            $q2->whereDate('created_at', '<=', $dataFim);
                        }
                    });
                });
            }

            $perPage = (int) $request->get('per_page', 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;
            $paginado = $query->paginate($perPage);

            $paginado->setCollection($paginado->getCollection()->map(function ($tarefa) {
                return $this->mapResumo($tarefa);
            }));

            return response()->json([
                'success' => true,
                'data' => $paginado
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao listar tarefas de contagem: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar tarefas de contagem.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            $tarefa = TarefaContagem::with([
                'capaInventario',
                'usuario',
                'supervisor',
                'produtos.produto',
                'historico.usuario',
            ])->withCount([
                'produtos as total_produtos',
                'produtos as produtos_contados' => function ($q) {
                    $q->whereNotNull('quantidade_contada');
                }
            ])->findOrFail($id);

            $erroAcesso = $this->validaAcessoTarefa($usuario, $tarefa);
            if ($erroAcesso) {
                return $erroAcesso;
            }

            $produtos = $tarefa->produtos->map(function ($item) {
                return [
                    'id_produto' => $item->id_produto,
                    'descricao' => $item->produto?->descricao,
                    'quantidade_contada' => $item->quantidade_contada,
                    'status' => $item->quantidade_contada === null ? 'pendente' : 'contado',
                ];
            });

            $historico = $tarefa->historico->map(function ($item) {
                return [
                    'id_historico' => $item->id_historico,
                    'acao' => $item->acao,
                    'descricao' => $item->descricao,
                    'usuario' => $item->usuario?->nome,
                    'data' => $item->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'id_tarefa' => $tarefa->id_tarefa,
                    'id_capa_inventario' => $tarefa->id_capa_inventario,
                    'inventario' => $tarefa->capaInventario ? [
                        'id_capa_inventario' => $tarefa->capaInventario->id_capa_inventario,
                        'descricao' => $tarefa->capaInventario->descricao,
                        'status' => $tarefa->capaInventario->status,
                    ] : null,
                    'usuario' => $tarefa->usuario ? [
                        'id_usuario' => $tarefa->usuario->id_usuario,
                        'nome' => $tarefa->usuario->nome,
                        'email' => $tarefa->usuario->email,
                    ] : null,
                    'supervisor' => $tarefa->supervisor ? [
                        'id_usuario' => $tarefa->supervisor->id_usuario,
                        'nome' => $tarefa->supervisor->nome,
                    ] : null,
                    'tipo_tarefa' => $tarefa->tipo_tarefa,
                    'status' => $tarefa->status,
                    'data_inicio' => $tarefa->data_inicio,
                    'data_fim' => $tarefa->data_fim,
                    'observacoes' => $tarefa->observacoes,
                    'created_at' => $tarefa->created_at,
                    'updated_at' => $tarefa->updated_at,
                    'total_produtos' => $tarefa->total_produtos ?? 0,
                    'produtos_contados' => $tarefa->produtos_contados ?? 0,
                    'produtos' => $produtos,
                    'historico' => $historico,
                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tarefa não encontrada.'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function store(StoreTarefaContagemRequest $request)
    {
        $usuario = $this->usuarioAutenticado();
        if (!$usuario) {
            return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->podeGerenciar($usuario)) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para criar tarefas.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $capa = CapaInventario::findOrFail($request->id_capa_inventario);
            $erroEmpresa = $this->validaAcessoEmpresa($usuario, $capa);
            if ($erroEmpresa) {
                return $erroEmpresa;
            }

            $responsavel = Usuario::findOrFail($request->id_usuario);
            if ((int) $responsavel->id_empresa !== (int) $capa->id_empresa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário responsável não pertence à empresa do inventário.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($request->filled('id_supervisor')) {
                $supervisor = Usuario::findOrFail($request->id_supervisor);
                if ((int) $supervisor->id_empresa !== (int) $capa->id_empresa) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Supervisor não pertence à empresa do inventário.'
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            DB::beginTransaction();

            $tarefa = TarefaContagem::create([
                'id_capa_inventario' => $request->id_capa_inventario,
                'id_usuario' => $request->id_usuario,
                'id_supervisor' => $request->id_supervisor,
                'tipo_tarefa' => $request->tipo_tarefa,
                'status' => 'pendente',
                'observacoes' => $request->observacoes,
            ]);

            if ($request->filled('produtos')) {
                $produtosInventario = DB::table('tb_inventario')
                    ->where('id_capa_inventario', $capa->id_capa_inventario)
                    ->whereIn('id_produto', $request->produtos)
                    ->pluck('id_produto')
                    ->all();

                $solicitados = array_values(array_unique($request->produtos));
                $faltando = array_diff($solicitados, $produtosInventario);

                if (!empty($faltando)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Um ou mais produtos nao pertencem ao inventario informado.'
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                $dataInsert = [];
                foreach ($produtosInventario as $produtoId) {
                    $dataInsert[] = [
                        'id_tarefa' => $tarefa->id_tarefa,
                        'id_produto' => $produtoId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($dataInsert)) {
                    TarefaContagemProduto::insertOrIgnore($dataInsert);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tarefa criada com sucesso.',
                'data' => [
                    'id_tarefa' => $tarefa->id_tarefa,
                    'id_capa_inventario' => $tarefa->id_capa_inventario,
                    'id_usuario' => $tarefa->id_usuario,
                    'status' => $tarefa->status
                ]
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar tarefa de contagem: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar tarefa de contagem.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function iniciar(IniciarTarefaRequest $request, $id)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            $tarefa = TarefaContagem::with('capaInventario')->findOrFail($id);
            $erroAcesso = $this->validaAcessoTarefa($usuario, $tarefa);
            if ($erroAcesso) {
                return $erroAcesso;
            }

            if (!in_array($tarefa->status, ['pendente', 'pausada'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível iniciar tarefa com status ' . $tarefa->status
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $tarefa->status = 'em_andamento';
            if ($request->filled('observacoes')) {
                $tarefa->observacoes = $this->anexarObservacao($tarefa->observacoes, $request->observacoes);
            }
            $tarefa->save();
            $tarefa->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Tarefa iniciada com sucesso.',
                'data' => [
                    'id_tarefa' => $tarefa->id_tarefa,
                    'status' => $tarefa->status,
                    'data_inicio' => $tarefa->data_inicio
                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao iniciar tarefa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar tarefa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function pausar(PausarTarefaRequest $request, $id)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            $tarefa = TarefaContagem::with('capaInventario')->findOrFail($id);
            $erroAcesso = $this->validaAcessoTarefa($usuario, $tarefa);
            if ($erroAcesso) {
                return $erroAcesso;
            }

            if ($tarefa->status !== 'em_andamento') {
                return response()->json([
                    'success' => false,
                    'message' => 'Só é possível pausar tarefas em andamento.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $tarefa->status = 'pausada';
            $tarefa->observacoes = $this->anexarObservacao($tarefa->observacoes, 'Pausa: ' . $request->motivo);
            $tarefa->save();
            $tarefa->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Tarefa pausada com sucesso.',
                'data' => [
                    'id_tarefa' => $tarefa->id_tarefa,
                    'status' => $tarefa->status
                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao pausar tarefa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao pausar tarefa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function retomar(RetomarTarefaRequest $request, $id)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            $tarefa = TarefaContagem::with('capaInventario')->findOrFail($id);
            $erroAcesso = $this->validaAcessoTarefa($usuario, $tarefa);
            if ($erroAcesso) {
                return $erroAcesso;
            }

            if ($tarefa->status !== 'pausada') {
                return response()->json([
                    'success' => false,
                    'message' => 'Só é possível retomar tarefas pausadas.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $tarefa->status = 'em_andamento';
            if ($request->filled('observacoes')) {
                $tarefa->observacoes = $this->anexarObservacao($tarefa->observacoes, $request->observacoes);
            }
            $tarefa->save();
            $tarefa->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Tarefa retomada com sucesso.',
                'data' => [
                    'id_tarefa' => $tarefa->id_tarefa,
                    'status' => $tarefa->status
                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao retomar tarefa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao retomar tarefa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function concluir(ConcluirTarefaRequest $request, $id)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            $tarefa = TarefaContagem::with('capaInventario')->findOrFail($id);
            $erroAcesso = $this->validaAcessoTarefa($usuario, $tarefa);
            if ($erroAcesso) {
                return $erroAcesso;
            }

            if ($tarefa->status !== 'em_andamento') {
                return response()->json([
                    'success' => false,
                    'message' => 'Só é possível concluir tarefas em andamento.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $totalProdutos = $tarefa->produtos()->count();
            $produtosContados = $tarefa->produtos()->whereNotNull('quantidade_contada')->count();
            $forcar = (bool) $request->get('forcar_conclusao', false);

            if ($totalProdutos > 0 && $produtosContados < $totalProdutos && !$forcar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Existem produtos pendentes. Finalize as contagens ou use forcar_conclusao.',
                    'data' => [
                        'total_produtos' => $totalProdutos,
                        'produtos_contados' => $produtosContados
                    ]
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $tarefa->status = 'concluida';
            $tarefa->observacoes = $this->anexarObservacao($tarefa->observacoes, 'Conclusão: ' . $request->observacoes);
            $tarefa->save();
            $tarefa->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Tarefa concluída com sucesso.',
                'data' => [
                    'id_tarefa' => $tarefa->id_tarefa,
                    'status' => $tarefa->status,
                    'data_fim' => $tarefa->data_fim,
                    'produtos_contados' => $produtosContados,
                    'total_produtos' => $totalProdutos
                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao concluir tarefa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao concluir tarefa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function cancelar(CancelarTarefaRequest $request, $id)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            $tarefa = TarefaContagem::with('capaInventario')->findOrFail($id);
            $erroAcesso = $this->validaAcessoTarefa($usuario, $tarefa);
            if ($erroAcesso) {
                return $erroAcesso;
            }

            if ($tarefa->status === 'concluida') {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível cancelar uma tarefa concluída.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $tarefa->status = 'cancelada';
            $tarefa->observacoes = $this->anexarObservacao($tarefa->observacoes, 'Cancelada: ' . $request->motivo);
            $tarefa->save();

            return response()->json([
                'success' => true,
                'message' => 'Tarefa cancelada com sucesso.'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao cancelar tarefa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar tarefa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function historico($id)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            $tarefa = TarefaContagem::with(['capaInventario'])->findOrFail($id);
            $erroAcesso = $this->validaAcessoTarefa($usuario, $tarefa);
            if ($erroAcesso) {
                return $erroAcesso;
            }

            $historico = $tarefa->historico()->with('usuario')->orderBy('created_at')->get()->map(function ($item) {
                return [
                    'id_historico' => $item->id_historico,
                    'acao' => $item->acao,
                    'descricao' => $item->descricao,
                    'usuario' => $item->usuario?->nome,
                    'data' => $item->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $historico
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao buscar histórico da tarefa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar histórico da tarefa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function porInventario($id_capa)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            $capa = CapaInventario::findOrFail($id_capa);
            $erroEmpresa = $this->validaAcessoEmpresa($usuario, $capa);
            if ($erroEmpresa) {
                return $erroEmpresa;
            }

            $query = TarefaContagem::with(['usuario'])
                ->where('id_capa_inventario', $id_capa)
                ->orderBy('data_inicio');

            if (!$this->podeGerenciar($usuario)) {
                $query->where('id_usuario', $usuario->id_usuario);
            }

            $tarefas = $query->get()->map(function ($tarefa) {
                return [
                    'id_tarefa' => $tarefa->id_tarefa,
                    'usuario' => $tarefa->usuario?->nome,
                    'status' => $tarefa->status,
                    'data_inicio' => $tarefa->data_inicio,
                    'data_fim' => $tarefa->data_fim,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $tarefas
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao listar tarefas por inventário: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar tarefas do inventário.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function porUsuario($id_usuario)
    {
        try {
            $usuario = $this->usuarioAutenticado();
            if (!$usuario) {
                return response()->json(['message' => 'Não autenticado.'], Response::HTTP_UNAUTHORIZED);
            }

            if (!$this->podeGerenciar($usuario) && (int) $usuario->id_usuario !== (int) $id_usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para consultar tarefas de outro usuário.'
                ], Response::HTTP_FORBIDDEN);
            }

            $usuarioAlvo = Usuario::findOrFail($id_usuario);
            if ($usuario->perfil !== 'super_admin' && (int) $usuarioAlvo->id_empresa !== (int) $usuario->id_empresa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não pertence à sua empresa.'
                ], Response::HTTP_FORBIDDEN);
            }

            $query = TarefaContagem::with(['capaInventario'])
                ->where('id_usuario', $id_usuario)
                ->orderBy('data_inicio', 'desc');

            $this->aplicaEscopoEmpresa($query, $usuario);

            $tarefas = $query->get()->map(function ($tarefa) {
                return [
                    'id_tarefa' => $tarefa->id_tarefa,
                    'inventario' => $tarefa->capaInventario?->descricao,
                    'tipo_tarefa' => $tarefa->tipo_tarefa,
                    'status' => $tarefa->status,
                    'data_inicio' => $tarefa->data_inicio,
                    'data_fim' => $tarefa->data_fim,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $tarefas
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao listar tarefas por usuário: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar tarefas do usuário.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
