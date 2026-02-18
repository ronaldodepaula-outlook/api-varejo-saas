<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon">
                <i class="bi bi-diagram-3"></i>
            </div>
            <div class="logo-text">NexusFlow</div>
        </div>
    </div>
    
    <div class="sidebar-nav">
        <?php
        // Mapeamento de segmentos para menus específicos
        $segmentoMenus = [
            'admin' => [
                'Dashboard' => ['home-admins'],
                'Administração' => ['admin-empresas', 'admin-filiais', 'admin-produtos', 'gestao_usuarios'],
                'Relatórios' => ['relatorios-gerais', 'relatorios-financeiros', 'relatorios-usuarios'],
                'Sistema' => ['configuracoes-sistema', 'backup', 'logs-sistema']
            ],
            'varejo' => [
                'Dashboard' => ['home-varejo'],
                'Vendas' => ['pdv-venda', 'vendas', 'orcamentos'],
                'Produtos' => ['catalogo-produtos', 'estoque', 'categorias'],
                'Clientes' => ['cadastro-clientes', 'fidelidade', 'vendas-clientes'],
                'Relatórios' => ['relatorios-vendas', 'relatorios-estoque', 'relatorios-financeiros']
            ],
            'ecommerce' => [
                'Dashboard' => ['home-ecommerce'],
                'Loja Virtual' => ['produtos-ecommerce', 'categorias-ecommerce', 'pedidos'],
                'Marketing' => ['campanhas', 'cupons-desconto', 'email-marketing'],
                'Clientes' => ['clientes-ecommerce', 'avaliacoes'],
                'Relatórios' => ['relatorios-vendas', 'relatorios-traffic', 'relatorios-conversao']
            ],
            'alimentacao' => [
                'Dashboard' => ['home-alimentacao'],
                'Pedidos' => ['pdv-venda', 'mesas', 'comandas'],
                'Cardápio' => ['cardapio', 'categorias-cardapio', 'promocoes'],
                'Estoque' => ['controle-estoque', 'receitas', 'fornecedores'],
                'Relatórios' => ['relatorios-vendas', 'relatorios-populares', 'relatorios-financeiros']
            ],
            'turismo_hotelaria' => [
                'Dashboard' => ['home-turismo-hotelaria'],
                'Reservas' => ['reservas', 'checkin-checkout', 'ocupacao'],
                'Hospedes' => ['cadastro-hospedes', 'historico-estadias'],
                'Acomodações' => ['quartos', 'tarifas', 'servicos'],
                'Relatórios' => ['relatorios-ocupacao', 'relatorios-receita', 'relatorios-hospedes']
            ],
            'imobiliario' => [
                'Dashboard' => ['home-imobiliario'],
                'Imóveis' => ['cadastro-imoveis', 'inventario', 'caracteristicas'],
                'Clientes' => ['proprietarios', 'inquilinos', 'interessados'],
                'Contratos' => ['contratos-locacao', 'contratos-venda', 'renovacoes'],
                'Visitas' => ['agendamento-visitas', 'acompanhamento-visitas'],
                'Relatórios' => ['relatorios-vendas', 'relatorios-alugueis', 'relatorios-visitas']
            ],
            'industria' => [
                'Dashboard' => ['home-industria'],
                'Produção' => ['ordens-producao', 'controle-qualidade', 'linhas-producao'],
                'Estoque' => ['materia-prima', 'produtos-acabados', 'controle-inventario'],
                'Fornecedores' => ['cadastro-fornecedores', 'compras', 'avaliacao-fornecedores'],
                'Manutenção' => ['manutencao-maquinas', 'calendario-manutencao'],
                'Relatórios' => ['relatorios-producao', 'relatorios-qualidade', 'relatorios-manutencao']
            ],
            'construcao' => [
                'Dashboard' => ['home-construcao'],
                'Projetos' => ['cadastro-projetos', 'etapas-projeto', 'cronograma'],
                'Materiais' => ['controle-materiais', 'requisicoes', 'fornecedores-construcao'],
                'Equipe' => ['funcionarios', 'alocacao-equipe', 'pontos-construcao'],
                'Orçamentos' => ['orcamentos-obra', 'controle-custos'],
                'Relatórios' => ['relatorios-andamento', 'relatorios-custos', 'relatorios-produtividade']
            ],
            'financeiro' => [
                'Dashboard' => ['home-financeiro'],
                'Transações' => ['lancamentos', 'transferencias', 'conciliacao'],
                'Contas' => ['contas-bancarias', 'cartoes', 'investimentos'],
                'Relatórios' => ['fluxo-caixa', 'demonstrativos', 'analise-financeira'],
                'Cobrança' => ['faturas', 'recebimentos', 'inadimplencia']
            ],
            'saude' => [
                'Dashboard' => ['home-saude'],
                'Pacientes' => ['cadastro-pacientes', 'prontuarios', 'agendamentos'],
                'Profissionais' => ['cadastro-medicos', 'especialidades', 'escalas'],
                'Clinica' => ['procedimentos', 'convenios', 'estoque-medicamentos'],
                'Financeiro' => ['faturas-saude', 'contas-receber-saude'],
                'Relatórios' => ['relatorios-atendimentos', 'relatorios-financeiros-saude']
            ],
            'educacao' => [
                'Dashboard' => ['home-educacao'],
                'Alunos' => ['cadastro-alunos', 'matriculas', 'historico-academico'],
                'Professores' => ['cadastro-professores', 'disciplinas', 'horarios'],
                'Acadêmico' => ['turmas', 'calendario-aulas', 'avaliacoes'],
                'Financeiro' => ['mensalidades', 'bolsas', 'contas-receber-educacao'],
                'Relatórios' => ['relatorios-matriculas', 'relatorios-desempenho', 'relatorios-financeiros-educacao']
            ],
            'logistica_transporte' => [
                'Dashboard' => ['home-logistica-transporte'],
                'Frota' => ['cadastro-veiculos', 'manutencao-frota', 'rastreamento'],
                'Rotas' => ['planejamento-rotas', 'otimizacao-cargas', 'entregas'],
                'Motoristas' => ['cadastro-motoristas', 'escalas', 'pontos-transporte'],
                'Relatórios' => ['relatorios-entregas', 'relatorios-custos-transporte', 'relatorios-frota']
            ],
            'seguranca' => [
                'Dashboard' => ['home-seguranca'],
                'Equipe' => ['cadastro-vigilantes', 'escalas-seguranca', 'pontos-seguranca'],
                'Ocorrências' => ['registro-ocorrencias', 'relatorios-ocorrencias', 'alertas'],
                'Clientes' => ['clientes-seguranca', 'contratos-seguranca'],
                'Relatórios' => ['relatorios-patrulha', 'relatorios-ocorrencias', 'relatorios-equipe']
            ],
            'outros' => [
                'Dashboard' => ['home-outros'],
                'Gestão' => ['clientes-outros', 'fornecedores-outros', 'estoque-outros'],
                'Financeiro' => ['contas-pagar-outros', 'contas-receber-outros'],
                'Relatórios' => ['relatorios-gerais-outros']
            ]
        ];

        // Helper to prettify label
        function pretty_label($s) {
            $s = str_replace(['home-', '_', '-'], ['', ' ', ' '], $s);
            $s = preg_replace('/\s+/', ' ', trim($s));
            $s = ucfirst($s);
            return $s;
        }

        // Icon maps for different sections
        $sectionIcons = [
            'Dashboard' => 'bi-speedometer2',
            'Administração' => 'bi-gear',
            'Vendas' => 'bi-cart',
            'Produtos' => 'bi-box',
            'Clientes' => 'bi-people',
            'Relatórios' => 'bi-graph-up',
            'Sistema' => 'bi-cpu',
            'Loja Virtual' => 'bi-shop',
            'Marketing' => 'bi-megaphone',
            'Pedidos' => 'bi-receipt',
            'Cardápio' => 'bi-menu-app',
            'Estoque' => 'bi-clipboard-data',
            'Reservas' => 'bi-calendar-check',
            'Hospedes' => 'bi-person-badge',
            'Acomodações' => 'bi-house-door',
            'Imóveis' => 'bi-building',
            'Contratos' => 'bi-file-earmark-text',
            'Visitas' => 'bi-eye',
            'Produção' => 'bi-gear-wide',
            'Fornecedores' => 'bi-truck',
            'Manutenção' => 'bi-tools',
            'Projetos' => 'bi-kanban',
            'Materiais' => 'bi-pallet',
            'Equipe' => 'bi-person-workspace',
            'Orçamentos' => 'bi-calculator',
            'Transações' => 'bi-arrow-left-right',
            'Contas' => 'bi-wallet',
            'Cobrança' => 'bi-credit-card',
            'Pacientes' => 'bi-person-heart',
            'Profissionais' => 'bi-person-badge',
            'Clinica' => 'bi-hospital',
            'Alunos' => 'bi-person-video',
            'Professores' => 'bi-person-standing',
            'Acadêmico' => 'bi-journal-text',
            'Frota' => 'bi-truck',
            'Rotas' => 'bi-signpost-split',
            'Motoristas' => 'bi-person-driving',
            'Ocorrências' => 'bi-exclamation-triangle',
            'Gestão' => 'bi-clipboard-check'
        ];

        // Get the current segment from session
        $currentSegment = $segmento ?? 'outros';

        // If segment not found in mapping, use 'outros'
        if (!isset($segmentoMenus[$currentSegment])) {
            $currentSegment = 'outros';
        }

        // Render menu for current segment
        foreach ($segmentoMenus[$currentSegment] as $section => $items) {
            if (count($items) === 0) continue;
            
            $sectionIcon = isset($sectionIcons[$section]) ? $sectionIcons[$section] : 'bi-folder';
            
            echo "<div class=\"nav-section\">";
            echo "<div class=\"nav-header\"><i class=\"bi $sectionIcon nav-header-icon\"></i><span class=\"nav-header-text\">$section</span></div>";

            foreach ($items as $item) {
                $label = pretty_label($item);
                $href = '?view=' . $item;
                $iconClass = 'bi-circle'; // Default icon
                
                // Try to find specific icon for this item
                foreach ($sectionIcons as $sectionName => $icon) {
                    if (stripos($item, strtolower($sectionName)) !== false) {
                        $iconClass = $icon;
                        break;
                    }
                }
                
                echo "<div class=\"nav-item\"><a href=\"$href\" class=\"nav-link\"><i class=\"bi $iconClass nav-icon\"></i><span class=\"nav-text\">$label</span></a></div>";
            }

            echo "</div>";
        }

        // Add common sections for all segments
        if ($currentSegment !== 'admin') {
            echo "<div class=\"nav-section\">";
            echo "<div class=\"nav-header\"><i class=\"bi bi-person nav-header-icon\"></i><span class=\"nav-header-text\">Minha Conta</span></div>";
            echo "<div class=\"nav-item\"><a href=\"?view=perfil\" class=\"nav-link\"><i class=\"bi bi-person nav-icon\"></i><span class=\"nav-text\">Meu Perfil</span></a></div>";
            echo "<div class=\"nav-item\"><a href=\"?view=configuracoes\" class=\"nav-link\"><i class=\"bi bi-gear nav-icon\"></i><span class=\"nav-text\">Configurações</span></a></div>";
            echo "</div>";
        }

        // Add debug section for development
        if ($currentSegment === 'admin' || (isset($_SESSION['debug_mode']) && $_SESSION['debug_mode'])) {
            echo "<div class=\"nav-section\">";
            echo "<div class=\"nav-header\"><i class=\"bi bi-bug nav-header-icon\"></i><span class=\"nav-header-text\">Desenvolvimento</span></div>";
            echo "<div class=\"nav-item\"><a href=\"?view=debug-session\" class=\"nav-link\"><i class=\"bi bi-bug nav-icon\"></i><span class=\"nav-text\">Debug Session</span></a></div>";
            echo "</div>";
        }
        ?>
    </div>
</nav>