<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;

class ApiDocsController extends Controller
{
    public function swagger()
    {
        return view('swagger', [
            'openapiUrl' => url('/api/openapi.json'),
        ]);
    }

    public function openapi(Request $request)
    {
        /** @var RouteCollection $routes */
        $routes = Route::getRoutes();

        $paths = [];
        $usedTags = [];

        foreach ($routes as $route) {
            $uri = $route->uri();
            $middlewares = $route->gatherMiddleware();
            $name = $route->getName();

            $isApiRoute = (
                strpos($uri, 'api/') === 0 ||
                in_array('api', $middlewares, true) ||
                ($name && strpos($name, 'api.') === 0)
            );

            if (!$isApiRoute) {
                continue;
            }

            if ($uri === 'api/openapi.json') {
                continue;
            }

            $methods = array_values(array_diff($route->methods(), ['HEAD', 'OPTIONS']));
            if (!$methods) {
                continue;
            }

            $path = '/' . $uri;

            foreach ($methods as $method) {
                $methodLower = strtolower($method);
                $tag = $this->resolveTag($uri, $route->getActionName());
                $usedTags[$tag] = true;

                $operation = [
                    'operationId' => $this->makeOperationId($method, $uri),
                    'summary' => $this->makeSummary($method, $uri, $route->getActionName()),
                    'tags' => [$tag],
                    'responses' => [
                        '200' => ['description' => 'OK'],
                    ],
                ];

                if (in_array('auth:sanctum', $middlewares, true)) {
                    $operation['security'] = [['bearerAuth' => []]];
                    $operation['description'] = 'Requer autenticação via Bearer token (Sanctum).';
                } else {
                    $operation['description'] = 'Acesso público (sem autenticação).';
                }

                $params = [];
                foreach ($route->parameterNames() as $param) {
                    $params[] = [
                        'name' => $param,
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'string'],
                    ];
                }

                if ($params) {
                    $operation['parameters'] = $params;
                }

                if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
                    $examplePayload = $this->examplePayload($method, $uri);
                    if ($examplePayload !== null) {
                        $operation['requestBody'] = [
                            'required' => in_array($method, ['POST', 'PUT', 'PATCH'], true),
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'example' => $examplePayload,
                                    ],
                                    'example' => $examplePayload,
                                ],
                            ],
                        ];
                    }
                }

                if ($tag === 'Senha') {
                    $operation['description'] = ($operation['description'] ?? '') . ' Token com validade de 24 horas.';
                    $operation['responses'] = $this->senhaResponses($uri, $operation['responses']);
                }

                $paths[$path][$methodLower] = $operation;
            }
        }

        ksort($paths);

        $tags = $this->buildTags(array_keys($usedTags));

        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'SaaS MultiEmpresas API',
                'version' => '1.0.0',
            ],
            'servers' => [
                ['url' => $request->getSchemeAndHttpHost() . $request->getBaseUrl()],
            ],
            'tags' => $tags,
            'paths' => $paths,
            'components' => [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT',
                    ],
                ],
            ],
        ];

        return response()->json($spec);
    }

    private function resolveTag(string $uri, ?string $actionName): string
    {
        $uri = ltrim($uri, '/');

        if (preg_match('#^api/(registrar|login|token|verificar-email|user)#', $uri)) {
            return 'Auth';
        }
        if (strpos($uri, 'password') !== false || strpos($uri, 'esqueci-senha') !== false || strpos($uri, 'redefinir-senha') !== false) {
            return 'Senha';
        }
        if (preg_match('#^api/(empresas|filiais|usuarios|licencas|assinaturas|pagamentos)#', $uri) ||
            preg_match('#^api/v1/(empresas|filiais|usuarios|licencas|assinaturas|pagamentos)#', $uri)) {
            return 'Core';
        }
        if (preg_match('#^api/v1/dashboard#', $uri)) {
            return 'Dashboard';
        }
        if (preg_match('#^api/v1/(categorias|secoes|grupos|subgrupos)#', $uri)) {
            return 'Catálogo';
        }
        if (preg_match('#^api/v1/.*produtos#', $uri)) {
            return 'Produtos';
        }
        if (preg_match('#^api/(capa-inventarios|inventarios|contagens)#', $uri) || preg_match('#^api/v1/(capas-inventario|inventarios)#', $uri)) {
            return 'Inventário';
        }
        if (preg_match('#^api/(estoque|estoques|movimentacoes)#', $uri)) {
            return 'Estoque';
        }
        if (preg_match('#^api/(capa-transferencias|transferencias)#', $uri)) {
            return 'Transferências';
        }
        if (preg_match('#^api/(nfe|sefaz)#', $uri)) {
            return 'NFE';
        }
        if (preg_match('#^api/(pdv|v1/empresas/.*/pdv)#', $uri)) {
            return 'PDV';
        }
        if (preg_match('#^api/(vendasAssistidas|vendasapi|debitos-clientes)#', $uri)) {
            return 'Vendas';
        }
        if (preg_match('#^api/(notificacoes|backup)#', $uri)) {
            return 'Notificações';
        }
        if (preg_match('#^api/v1/rotas#', $uri)) {
            return 'Utilitários';
        }
        if (preg_match('#^api/diagnostico#', $uri)) {
            return 'Diagnóstico';
        }
        if (preg_match('#^api/(debug|teste-)#', $uri)) {
            return 'Debug/Testes';
        }

        return 'Outros';
    }

    private function buildTags(array $usedTags): array
    {
        $tagDescriptions = [
            'Auth' => 'Fluxo de autenticação e ativação de conta (cadastro, verificação de e-mail e login).',
            'Senha' => 'Fluxo de recuperação e redefinição de senha.',
            'Core' => 'Cadastros principais: empresas, filiais, usuários, licenças, assinaturas e pagamentos.',
            'Dashboard' => 'Resumo operacional do sistema.',
            'Catálogo' => 'Catálogo hierárquico: categorias, seções, grupos e subgrupos.',
            'Produtos' => 'Gestão e filtros de produtos.',
            'Inventário' => 'Capas, itens de inventário e contagens.',
            'Estoque' => 'Saldos, movimentações e consultas de estoque.',
            'Transferências' => 'Capas e transferências de estoque.',
            'NFE' => 'Módulo de NF-e/DANFE e integrações SEFAZ.',
            'PDV' => 'Caixa e vendas no ponto de venda.',
            'Vendas' => 'Vendas assistidas, clientes e débitos.',
            'Notificações' => 'Alertas e execução de backup.',
            'Utilitários' => 'Listagem de rotas e utilidades do sistema.',
            'Diagnóstico' => 'Diagnósticos e validações de dados.',
            'Debug/Testes' => 'Rotas de depuração e testes internos.',
            'Outros' => 'Endpoints não categorizados.',
        ];

        $order = [
            'Auth',
            'Senha',
            'Core',
            'Dashboard',
            'Catálogo',
            'Produtos',
            'Inventário',
            'Estoque',
            'Transferências',
            'NFE',
            'PDV',
            'Vendas',
            'Notificações',
            'Utilitários',
            'Diagnóstico',
            'Debug/Testes',
            'Outros',
        ];

        $tags = [];
        foreach ($order as $tag) {
            if (!in_array($tag, $usedTags, true)) {
                continue;
            }
            $tags[] = [
                'name' => $tag,
                'description' => $tagDescriptions[$tag] ?? '',
            ];
        }

        return $tags;
    }

    private function makeSummary(string $method, string $uri, string $actionName): string
    {
        $method = strtoupper($method);
        $uri = '/' . ltrim($uri, '/');
        return $method . ' ' . $uri;
    }

    private function makeOperationId(string $method, string $uri): string
    {
        $id = strtolower($method) . '_' . $uri;
        $id = preg_replace('/[^a-z0-9_]/', '_', $id);
        $id = preg_replace('/_+/', '_', $id);
        return trim($id, '_');
    }

    private function examplePayload(string $method, string $uri): ?array
    {
        $path = ltrim($uri, '/');
        if (str_starts_with($path, 'api/')) {
            $path = substr($path, 4);
        }

        $examples = $this->payloadExamples();

        // Auth
        if ($path === 'registrar') {
            return $examples['registrar'];
        }
        if ($path === 'login' || $path === 'token') {
            return $examples['login'];
        }

        // Senha
        if ($path === 'password/solicitar-reset' || $path === 'v1/password/solicitar-reset' || $path === 'v1/password/email' || $path === 'v1/esqueci-senha') {
            return $examples['senha_solicitar'];
        }
        if ($path === 'v1/password/validar-token') {
            return $examples['senha_validar'];
        }
        if ($path === 'v1/password/resetar-senha') {
            return $examples['senha_resetar'];
        }
        if (preg_match('#^v1/password/reset/#', $path)) {
            return $examples['senha_reset_route'];
        }
        if ($path === 'v1/redefinir-senha') {
            return $examples['senha_resetar'];
        }

        // Core
        if (preg_match('#^(v1/)?empresas($|/[^/]+$)#', $path)) {
            return $examples['empresa'];
        }
        if (preg_match('#^(v1/)?filiais($|/[^/]+$)#', $path)) {
            return $examples['filial'];
        }
        if (preg_match('#^(v1/)?usuarios($|/[^/]+$)#', $path)) {
            return $examples['usuario'];
        }
        if (preg_match('#^(v1/)?licencas($|/[^/]+$)#', $path)) {
            return $examples['licenca'];
        }
        if (preg_match('#^(v1/)?assinaturas($|/[^/]+$)#', $path)) {
            return $examples['assinatura'];
        }
        if (preg_match('#^(v1/)?pagamentos($|/[^/]+$)#', $path)) {
            return $examples['pagamento'];
        }

        // Catálogo e Produtos (v1)
        if (preg_match('#^v1/categorias($|/[^/]+$)#', $path)) {
            return $examples['categoria'];
        }
        if (preg_match('#^v1/secoes($|/[^/]+$)#', $path)) {
            return $examples['secao'];
        }
        if (preg_match('#^v1/grupos($|/[^/]+$)#', $path)) {
            return $examples['grupo'];
        }
        if (preg_match('#^v1/subgrupos($|/[^/]+$)#', $path)) {
            return $examples['subgrupo'];
        }
        if (preg_match('#^v1/produtos($|/[^/]+$)#', $path)) {
            return $examples['produto'];
        }

        // Fornecedores (v1)
        if (preg_match('#^v1/fornecedores($|/[^/]+$)#', $path)) {
            return $examples['fornecedor'];
        }

        // Inventário e Contagens
        if (preg_match('#^capa-inventarios($|/[^/]+$)#', $path)) {
            return $examples['capa_inventario'];
        }
        if (preg_match('#^inventarios($|/[^/]+$)#', $path)) {
            return $examples['inventario'];
        }
        if (preg_match('#^contagens($|/[^/]+$)#', $path)) {
            return $examples['contagem'];
        }

        // Estoque e Movimentações
        if (preg_match('#^estoques($|/[^/]+$)#', $path)) {
            return $examples['estoque'];
        }
        if (preg_match('#^movimentacoes($|/[^/]+$)#', $path)) {
            return $examples['movimentacao'];
        }

        // Transferências
        if (preg_match('#^capa-transferencias($|/[^/]+$)#', $path)) {
            return $examples['capa_transferencia'];
        }
        if (preg_match('#^transferencias($|/[^/]+$)#', $path)) {
            return $examples['transferencia'];
        }

        // NF-e
        if ($path === 'nfe/importar' || $path === 'nfe/importar-xml') {
            return $examples['nfe_importar'];
        }
        if (preg_match('#^nfe/emitentes($|/[^/]+$)#', $path)) {
            return $examples['nfe_emitente'];
        }
        if (preg_match('#^nfe/destinatarios($|/[^/]+$)#', $path)) {
            return $examples['nfe_destinatario'];
        }
        if (preg_match('#^nfe/itens($|/[^/]+$)#', $path)) {
            return $examples['nfe_item'];
        }
        if (preg_match('#^nfe/impostos($|/[^/]+$)#', $path)) {
            return $examples['nfe_imposto'];
        }
        if (preg_match('#^nfe/transporte($|/[^/]+$)#', $path)) {
            return $examples['nfe_transporte'];
        }
        if (preg_match('#^nfe/cobrancas($|/[^/]+$)#', $path)) {
            return $examples['nfe_cobranca'];
        }
        if (preg_match('#^nfe/duplicatas($|/[^/]+$)#', $path)) {
            return $examples['nfe_duplicata'];
        }
        if (preg_match('#^nfe/pagamentos($|/[^/]+$)#', $path)) {
            return $examples['nfe_pagamento'];
        }
        if (preg_match('#^nfe/informacoes-adicionais($|/[^/]+$)#', $path)) {
            return $examples['nfe_informacoes'];
        }
        if ($path === 'nfe' || preg_match('#^nfe/[^/]+$#', $path)) {
            return $examples['nfe_cabecalho'];
        }

        // PDV
        if ($path === 'pdv/caixas/abrir') {
            return $examples['pdv_abertura'];
        }
        if ($path === 'pdv/vendas') {
            return $examples['pdv_venda'];
        }
        if (preg_match('#^v1/empresas/[^/]+/pdv/caixas/abertura$#', $path)) {
            return $examples['pdv_abertura'];
        }

        // Fornecedor-Produto relations (v1)
        if (preg_match('#^v1/fornecedor-produto($|/.*$)#', $path)) {
            return $examples['fornecedor_produto'];
        }

        // Compras (cotacoes, pedidos, entradas)
        if (preg_match('#^v1/.*compras.*#', $path) || preg_match('#^compras/.*#', $path)) {
            return $examples['compras'];
        }

        // Vendas assistidas
        if (preg_match('#^vendasAssistidas/clientes($|/[^/]+$)#', $path) || preg_match('#^vendasapi/clientes($|/[^/]+$)#', $path)) {
            return $examples['cliente'];
        }
        if (preg_match('#^vendasAssistidas/assistidas($|/[^/]+$)#', $path) || preg_match('#^vendasapi/assistidas($|/[^/]+$)#', $path)) {
            return $examples['venda_assistida'];
        }
        if (preg_match('#^vendasAssistidas/itens-assistida($|/[^/]+$)#', $path)) {
            return $examples['item_venda_assistida'];
        }
        if (preg_match('#^debitos-clientes/[^/]+$#', $path)) {
            return $examples['debito_update'];
        }

        // DELETE que requer payload (cliente)
        if ($method === 'DELETE' && preg_match('#^vendasAssistidas/clientes/[^/]+$#', $path)) {
            return $examples['cliente_delete'];
        }

        return null;
    }

    private function senhaResponses(string $uri, array $baseResponses): array
    {
        $path = '/' . ltrim($uri, '/');
        $responses = $baseResponses;

        if (strpos($path, 'password/validar-token') !== false) {
            $responses['200'] = [
                'description' => 'Token valido.',
                'content' => [
                    'application/json' => [
                        'example' => ['valid' => true],
                    ],
                ],
            ];
            $responses['404'] = [
                'description' => 'Token invalido ou expirado.',
                'content' => [
                    'application/json' => [
                        'example' => ['valid' => false],
                    ],
                ],
            ];
            return $responses;
        }

        if (strpos($path, 'password/resetar-senha') !== false || strpos($path, 'redefinir-senha') !== false) {
            $responses['200'] = [
                'description' => 'Senha redefinida.',
                'content' => [
                    'application/json' => [
                        'example' => ['mensagem' => 'Senha redefinida com sucesso.'],
                    ],
                ],
            ];
            $responses['400'] = [
                'description' => 'Token invalido ou expirado.',
                'content' => [
                    'application/json' => [
                        'example' => ['erro' => 'Token invalido ou expirado.'],
                    ],
                ],
            ];
            $responses['422'] = [
                'description' => 'Dados invalidos.',
                'content' => [
                    'application/json' => [
                        'example' => [
                            'message' => 'Dados invalidos.',
                            'errors' => ['password' => ['The password field is required.']],
                        ],
                    ],
                ],
            ];
            return $responses;
        }

        if (strpos($path, 'esqueci-senha') !== false || strpos($path, 'password/solicitar-reset') !== false || strpos($path, 'password/email') !== false) {
            $responses['200'] = [
                'description' => 'E-mail de redefinicao enviado.',
                'content' => [
                    'application/json' => [
                        'example' => ['mensagem' => 'E-mail de redefinicao enviado.'],
                    ],
                ],
            ];
            $responses['404'] = [
                'description' => 'E-mail nao encontrado.',
                'content' => [
                    'application/json' => [
                        'example' => ['erro' => 'E-mail nao encontrado.'],
                    ],
                ],
            ];
            $responses['500'] = [
                'description' => 'Falha ao enviar e-mail.',
                'content' => [
                    'application/json' => [
                        'example' => ['erro' => 'Falha ao enviar e-mail de redefinicao.'],
                    ],
                ],
            ];
            return $responses;
        }

        return $responses;
    }

    private function payloadExamples(): array
    {
        return [
            'registrar' => [
                'empresa' => [
                    'nome_empresa' => 'Empresa Exemplo',
                    'cnpj' => '12345678000199',
                    'email_empresa' => 'contato@empresa.com',
                    'telefone' => '11999999999',
                    'website' => 'https://empresa.com',
                    'endereco' => 'Rua Exemplo, 100',
                    'cep' => '01000-000',
                    'cidade' => 'São Paulo',
                    'estado' => 'SP',
                    'segmento' => 'varejo',
                ],
                'usuario' => [
                    'nome' => 'João Silva',
                    'email' => 'joao@empresa.com',
                    'senha' => '123456',
                    'aceitou_termos' => true,
                    'newsletter' => false,
                ],
            ],
            'login' => [
                'email' => 'joao@empresa.com',
                'senha' => '123456',
            ],
            'senha_solicitar' => [
                'email' => 'joao@empresa.com',
            ],
            'senha_validar' => [
                'email' => 'joao@empresa.com',
                'token' => 'TOKEN_AQUI',
            ],
            'senha_resetar' => [
                'email' => 'joao@empresa.com',
                'token' => 'TOKEN_AQUI',
                'senha' => 'NovaSenha123',
                'senha_confirmation' => 'NovaSenha123',
            ],
            'senha_reset_route' => [
                'email' => 'joao@empresa.com',
                'password' => 'NovaSenha123',
                'password_confirmation' => 'NovaSenha123',
            ],
            'empresa' => [
                'nome_empresa' => 'Empresa Exemplo',
                'cnpj' => '12345678000199',
                'email_empresa' => 'contato@empresa.com',
                'telefone' => '11999999999',
                'website' => 'https://empresa.com',
                'endereco' => 'Rua Exemplo, 100',
                'cep' => '01000-000',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'segmento' => 'varejo',
                'status' => 'ativa',
            ],
            'filial' => [
                'id_empresa' => 1,
                'nome_filial' => 'Filial Centro',
                'endereco' => 'Rua Central, 200',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '01000-000',
                'data_cadastro' => '2025-10-05 12:00:00',
            ],
            'usuario' => [
                'id_empresa' => 1,
                'nome' => 'João Silva',
                'email' => 'joao@empresa.com',
                'senha' => '123456',
                'perfil' => 'usuario',
                'ativo' => true,
                'aceitou_termos' => true,
                'newsletter' => false,
            ],
            'licenca' => [
                'id_empresa' => 1,
                'plano' => 'trial',
                'data_inicio' => '2025-01-01',
                'data_fim' => '2025-04-01',
                'status' => 'ativa',
            ],
            'assinatura' => [
                'id_empresa' => 1,
                'plano' => 'mensal',
                'valor' => 199.90,
                'data_inicio' => '2025-01-01',
                'data_fim' => '2025-02-01',
                'status' => 'ativa',
            ],
            'pagamento' => [
                'id_assinatura' => 1,
                'valor' => 199.90,
                'data_pagamento' => '2025-01-01',
                'metodo' => 'cartao',
                'status' => 'aprovado',
            ],
            'categoria' => [
                'id_empresa' => 1,
                'nome_categoria' => 'Alimentos',
                'descricao' => 'Produtos alimentícios',
            ],
            'secao' => [
                'id_empresa' => 1,
                'id_categoria' => 1,
                'nome_secao' => 'Bebidas',
                'descricao' => 'Seção de bebidas',
            ],
            'grupo' => [
                'id_empresa' => 1,
                'id_secao' => 1,
                'nome_grupo' => 'Refrigerantes',
                'descricao' => 'Grupo de refrigerantes',
            ],
            'subgrupo' => [
                'id_empresa' => 1,
                'id_grupo' => 1,
                'nome_subgrupo' => 'Cola',
                'descricao' => 'Subgrupo de colas',
            ],
            'produto' => [
                'id_empresa' => 1,
                'id_categoria' => 1,
                'id_secao' => 1,
                'id_grupo' => 1,
                'id_subgrupo' => 1,
                'descricao' => 'Refrigerante Cola 2L',
                'codigo_barras' => '7890000000000',
                'unidade_medida' => 'UN',
                'preco_custo' => 5.50,
                'preco_venda' => 9.90,
                'ativo' => true,
            ],
            'fornecedor' => [
                'id_empresa' => 1,
                'razao_social' => 'PAINE PRODUCAO E COMERCIO DE PAES ARTESANAIS LTDA',
                'nome_fantasia' => 'PAINE PRODUCAO',
                'cnpj' => '31006488000198',
                'inscricao_estadual' => null,
                'contato' => 'Nome do Contato',
                'telefone' => '85999990000',
                'email' => 'contato@fornecedor.com',
                'endereco' => 'Rua Exemplo, 100',
                'cidade' => 'Fortaleza',
                'estado' => 'CE',
                'cep' => '60000-000',
                'status' => 'ativo',
            ],
            'fornecedor_produto' => [
                'id_fornecedor' => 7,
                'id_produto' => 10,
                'status' => 'ativo'
            ],
            'compras' => [
                'cotacao' => [
                    'id_empresa' => 1,
                    'id_filial' => 1,
                    'data_cotacao' => '2026-02-19',
                    'data_validade' => '2026-03-19',
                    'observacoes' => 'Solicitar cotações para materiais'
                ],
                'cotacao_item' => [
                    'id_produto' => 10,
                    'quantidade' => 100,
                    'unidade_medida' => 'UN'
                ],
                'cotacao_fornecedor' => [
                    'id_fornecedor' => 7
                ],
                'cotacao_resposta' => [
                    'id_produto' => 10,
                    'quantidade' => 100,
                    'preco_unitario' => 12.50,
                    'prazo_entrega_item' => 5,
                    'selecionado' => 0
                ],
                'pedido' => [
                    'id_empresa' => 1,
                    'id_filial' => 1,
                    'id_fornecedor' => 7,
                    'data_pedido' => '2026-02-19',
                    'valor_total' => 1250.00,
                    'status' => 'rascunho'
                ],
                'pedido_item' => [
                    'id_produto' => 10,
                    'quantidade' => 100,
                    'preco_unitario' => 12.50
                ],
                'entrada' => [
                    'id_empresa' => 1,
                    'id_filial' => 1,
                    'id_fornecedor' => 7,
                    'data_entrada' => '2026-02-19 10:00:00',
                    'data_recebimento' => '2026-02-19 10:05:00',
                    'tipo_entrada' => 'pedido',
                    'valor_total' => 1250.00
                ],
                'entrada_item' => [
                    'id_produto' => 10,
                    'quantidade_recebida' => 100,
                    'preco_unitario' => 12.50
                ]
            ],
            'capa_inventario' => [
                'id_empresa' => 1,
                'id_filial' => 1,
                'descricao' => 'Inventário Mensal - Janeiro',
                'data_inicio' => '2025-01-10',
                'status' => 'em_andamento',
                'observacao' => 'Inventário mensal',
                'id_usuario' => 1,
            ],
            'inventario' => [
                'id_capa_inventario' => 1,
                'id_empresa' => 1,
                'id_filial' => 1,
                'id_produto' => 10,
                'quantidade_fisica' => 100,
                'quantidade_sistema' => 98,
                'motivo' => 'Ajuste de contagem',
                'data_inventario' => '2025-01-10T10:00:00Z',
                'id_usuario' => 1,
            ],
            'contagem' => [
                'id_inventario' => 1,
                'id_empresa' => 1,
                'id_filial' => 1,
                'id_produto' => 10,
                'tipo_operacao' => 'Adicionar',
                'quantidade' => 2,
                'observacao' => 'Contagem manual',
                'id_usuario' => 1,
            ],
            'estoque' => [
                'id_empresa' => 1,
                'id_filial' => 1,
                'id_produto' => 10,
                'quantidade' => 120,
                'estoque_minimo' => 10,
                'estoque_maximo' => 500,
            ],
            'movimentacao' => [
                'id_empresa' => 1,
                'id_filial' => 1,
                'id_produto' => 10,
                'tipo_movimentacao' => 'entrada',
                'origem' => 'nota_fiscal',
                'id_referencia' => 123,
                'quantidade' => 50,
                'saldo_anterior' => 100,
                'saldo_atual' => 150,
                'custo_unitario' => 5.50,
                'observacao' => 'Entrada por NF',
                'id_usuario' => 1,
            ],
            'capa_transferencia' => [
                'id_empresa' => 1,
                'id_filial_origem' => 1,
                'id_filial_destino' => 2,
                'data_transferencia' => '2025-01-15',
                'status' => 'pendente',
                'observacao' => 'Transferência entre filiais',
                'id_usuario' => 1,
            ],
            'transferencia' => [
                'id_empresa' => 1,
                'id_filial_origem' => 1,
                'id_filial_destino' => 2,
                'id_produto' => 10,
                'quantidade' => 5,
                'status' => 'pendente',
                'observacao' => 'Reposição',
            ],
            'nfe_cabecalho' => [
                'id_empresa' => 1,
                'id_filial' => 1,
                'cUF' => '35',
                'cNF' => '12345678',
                'natOp' => 'Venda de mercadoria',
                'mods' => '55',
                'serie' => '1',
                'nNF' => '1234',
                'dhEmi' => '2025-01-01T10:00:00-03:00',
                'tpNF' => '1',
                'idDest' => '1',
                'cMunFG' => '3550308',
                'tpImp' => '1',
                'tpEmis' => '1',
                'cDV' => '0',
                'tpAmb' => '2',
                'finNFe' => '1',
                'indFinal' => '1',
                'indPres' => '1',
                'procEmi' => '0',
                'verProc' => '1.0',
                'valor_total' => 150.00,
                'chave_acesso' => '35150112345678000199550010000012341000012345',
            ],
            'nfe_importar' => [
                'xml_base64' => 'BASE64_DO_XML_AQUI',
            ],
            'nfe_emitente' => [
                'id_nfe' => 1,
                'CNPJ' => '12345678000199',
                'xNome' => 'Empresa Exemplo LTDA',
                'xFant' => 'Empresa Exemplo',
                'xLgr' => 'Rua Exemplo',
                'nro' => '100',
                'xBairro' => 'Centro',
                'cMun' => '3550308',
                'xMun' => 'São Paulo',
                'UF' => 'SP',
                'CEP' => '01000-000',
                'cPais' => '1058',
                'xPais' => 'BRASIL',
                'fone' => '1130000000',
                'IE' => '123456789',
                'CRT' => '3',
            ],
            'nfe_destinatario' => [
                'id_nfe' => 1,
                'CNPJ' => '00987654000100',
                'xNome' => 'Cliente Exemplo',
                'xLgr' => 'Rua Cliente',
                'nro' => '50',
                'xBairro' => 'Bairro',
                'cMun' => '3550308',
                'xMun' => 'São Paulo',
                'UF' => 'SP',
                'CEP' => '01000-000',
                'cPais' => '1058',
                'xPais' => 'BRASIL',
                'fone' => '11999999999',
                'indIEDest' => '1',
                'IE' => '123456789',
                'email' => 'cliente@exemplo.com',
            ],
            'nfe_item' => [
                'id_nfe' => 1,
                'nItem' => 1,
                'cProd' => 'P001',
                'cEAN' => '7890000000000',
                'xProd' => 'Produto Exemplo',
                'NCM' => '22030000',
                'CFOP' => '5102',
                'uCom' => 'UN',
                'qCom' => 2,
                'vUnCom' => 10.00,
                'vProd' => 20.00,
            ],
            'nfe_imposto' => [
                'id_item' => 1,
                'vTotTrib' => 2.50,
                'orig' => '0',
                'CSOSN' => '102',
                'cEnq' => '999',
                'CST' => '00',
                'vBC' => 20.00,
                'pIPI' => 5.0,
                'vIPI' => 1.00,
                'vPIS' => 0.30,
                'vCOFINS' => 0.70,
            ],
            'nfe_transporte' => [
                'id_nfe' => 1,
                'modFrete' => '0',
                'CNPJ' => '12345678000199',
                'xNome' => 'Transportadora Exemplo',
                'IE' => '123456789',
                'xEnder' => 'Rua Transporte, 10',
                'xMun' => 'São Paulo',
                'UF' => 'SP',
                'esp' => 'CX',
                'pesoL' => 10.5,
                'pesoB' => 11.0,
            ],
            'nfe_cobranca' => [
                'id_nfe' => 1,
                'nFat' => '123',
                'vOrig' => 100.00,
                'vDesc' => 0.00,
                'vLiq' => 100.00,
            ],
            'nfe_duplicata' => [
                'id_cobranca' => 1,
                'nDup' => '001',
                'dVenc' => '2025-01-10',
                'vDup' => 50.00,
            ],
            'nfe_pagamento' => [
                'id_nfe' => 1,
                'indPag' => '0',
                'tPag' => '01',
                'vPag' => 100.00,
            ],
            'nfe_informacoes' => [
                'id_nfe' => 1,
                'infCpl' => 'Informações complementares da NF-e',
            ],
            'pdv_abertura' => [
                'id_empresa' => 1,
                'id_filial' => 1,
                'id_usuario' => 1,
                'valor_abertura' => 200.00,
            ],
            'pdv_venda' => [
                'id_caixa' => 1,
                'id_empresa' => 1,
                'id_filial' => 1,
                'valor_total' => 150.00,
                'tipo_venda' => 'venda',
                'itens' => [
                    [
                        'id_produto' => 10,
                        'quantidade' => 2,
                        'preco_unitario' => 50.00,
                    ],
                ],
                'pagamentos' => [
                    [
                        'forma_pagamento' => 'dinheiro',
                        'valor_pago' => 150.00,
                        'valor_troco' => 0.00,
                    ],
                ],
            ],
            'cliente' => [
                'id_empresa' => 1,
                'id_usuario' => 1,
                'nome_cliente' => 'Maria Cliente',
                'email' => 'maria@exemplo.com',
                'telefone' => '11999999999',
                'whatsapp' => '11999999999',
                'endereco' => 'Rua Cliente, 50',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '01000-000',
                'document_type' => 'cpf',
                'consent_marketing' => 'sim',
                'classificacao' => 'bronze',
                'status' => 'ativo',
            ],
            'cliente_delete' => [
                'id_usuario' => 1,
            ],
            'venda_assistida' => [
                'id_empresa' => 1,
                'id_filial' => 1,
                'id_cliente' => 10,
                'id_usuario' => 1,
                'tipo_venda' => 'venda',
                'forma_pagamento' => 'cartao',
                'valor_total' => 150.00,
                'observacao' => 'Venda balcão',
                'itens' => [
                    [
                        'id_produto' => 10,
                        'quantidade' => 2,
                        'preco_unitario' => 50.00,
                    ],
                ],
            ],
            'item_venda_assistida' => [
                'id_venda' => 1,
                'id_empresa' => 1,
                'id_filial' => 1,
                'id_produto' => 10,
                'quantidade' => 2,
                'preco_unitario' => 50.00,
            ],
            'debito_update' => [
                'status' => 'pago',
                'data_pagamento' => '2025-01-10',
            ],
        ];
    }
}
