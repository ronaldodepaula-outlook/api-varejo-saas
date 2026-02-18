<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - NexusFlow</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- NexusFlow CSS -->
    <link href="assets/css/nexusflow.css" rel="stylesheet">
    
    <style>
        .auth-card {
            max-width: 600px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--border-light);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin: 0 0.5rem;
            position: relative;
        }
        
        .step.active {
            background: var(--primary-blue);
            color: var(--white);
        }
        
        .step.completed {
            background: var(--success-green);
            color: var(--white);
        }
        
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 40px;
            height: 2px;
            background: var(--border-light);
            transform: translateY(-50%);
        }
        
        .step.completed:not(:last-child)::after {
            background: var(--success-green);
        }
        
        .form-section {
            display: none;
        }
        
        .form-section.active {
            display: block;
        }
        
        .trial-info {
            background: linear-gradient(135deg, var(--success-green), #27ae60);
            color: var(--white);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .segmento-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .segmento-option {
            border: 2px solid var(--border-light);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all var(--transition-fast);
        }
        
        .segmento-option:hover {
            border-color: var(--primary-blue);
            background: rgba(52, 152, 219, 0.05);
        }
        
        .segmento-option.selected {
            border-color: var(--primary-blue);
            background: rgba(52, 152, 219, 0.1);
        }
        
        .segmento-option i {
            font-size: 2rem;
            color: var(--primary-blue);
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<?php
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empresa = [
        'nome_empresa' => $_POST['nome_empresa'] ?? '',
        'cnpj' => $_POST['cnpj'] ?? '',
        'email_empresa' => $_POST['email_empresa'] ?? '',
        'telefone' => $_POST['telefone'] ?? '',
        'website' => $_POST['website'] ?? '',
        'endereco' => $_POST['endereco'] ?? '',
        'cep' => $_POST['cep'] ?? '',
        'cidade' => $_POST['cidade'] ?? '',
        'estado' => $_POST['estado'] ?? '',
        'segmento' => $_POST['segmento'] ?? ''
    ];
    $usuario = [
        'nome' => $_POST['nome'] ?? '',
        'email' => $_POST['email'] ?? '',
        'senha' => $_POST['senha'] ?? '',
        'aceitou_termos' => isset($_POST['aceitou_termos']) ? 1 : 0,
        'newsletter' => isset($_POST['newsletter']) ? 1 : 0
    ];
    // Campos obrigatórios
    if (empty($empresa['nome_empresa']) || empty($usuario['nome']) || empty($usuario['email']) || empty($usuario['senha'])) {
        $error = 'Preencha todos os campos obrigatórios.';
    } else {
        $data = json_encode(['empresa' => $empresa, 'usuario' => $usuario]);
        $ch = curl_init('http://localhost/saas-multiempresas-api/public/api/registrar');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $resp = json_decode($response, true);
        if ($httpcode === 201) {
            header('Location: cadastro-sucesso.html');
            exit;
        } else {
            $error = $resp['message'] ?? 'Erro ao cadastrar.';
        }
    }
}
?>
<body>
    <div class="auth-container d-flex align-items-center justify-content-center p-4">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <h4 class="mb-0">Cadastre sua Empresa</h4>
                <p class="mb-0 opacity-75">3 meses grátis para começar</p>
            </div>
            
            <div class="auth-body">
                <!-- Indicador de Passos -->
                <div class="step-indicator">
                    <div class="step active" data-step="1">1</div>
                    <div class="step" data-step="2">2</div>
                    <div class="step" data-step="3">3</div>
                </div>
                 <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
                <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
                <!--<form id="registerForm"> -->
                <form method="post">
                    <!-- Passo 1: Dados da Empresa -->
                    <div class="form-section active" data-section="1">
                        <h5 class="mb-3">Dados da Empresa</h5>
                        
                        <div class="trial-info">
                            <i class="bi bi-gift me-2"></i>
                            <strong>3 meses gratuitos</strong> para testar todas as funcionalidades!
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="nome_empresa" name="nome_empresa" placeholder="Nome da Empresa" required>
                                    <label for="nome_empresa">Nome da Empresa</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="cnpj" name="cnpj" placeholder="cnpj" required>
                                    <label for="cnpj">CNPJ</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email_empresa" name="email_empresa" placeholder="E-mail da Empresa" required>
                            <label for="email_empresa">E-mail da Empresa</label>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="Telefone" required>
                                    <label for="telefone">Telefone</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="website" name="website" placeholder="Website (opcional)">
                                    <label for="website">Website (opcional)</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" onclick="nextStep()">
                                Próximo <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Passo 2: segmentoo e Endereço -->
                    <div class="form-section" data-section="2">
                        <h5 class="mb-3">segmentoo e Localização</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Selecione o segmentoo da sua empresa:</label>
                            <div class="segmento-grid">
                                <div class="segmento-option" data-segmento="varejo">
                                    <i class="bi bi-shop"></i>
                                    <div>Varejo</div>
                                </div>
                                <div class="segmento-option" data-segmento="industria">
                                    <i class="bi bi-gear-fill"></i>
                                    <div>Indústria</div>
                                </div>
                                <div class="segmento-option" data-segmento="construcao">
                                    <i class="bi bi-building"></i>
                                    <div>Construção</div>
                                </div>
                                <div class="segmento-option" data-segmento="financeiro">
                                    <i class="bi bi-bank"></i>
                                    <div>Financeiro</div>
                                </div>
                                <div class="segmento-option" data-segmento="marketing">
                                    <i class="bi bi-megaphone"></i>
                                    <div>Marketing</div>
                                </div>
                                <div class="segmento-option" data-segmento="tecnologia">
                                    <i class="bi bi-laptop"></i>
                                    <div>Tecnologia</div>
                                </div>
                                <div class="segmento-option" data-segmento="saude">
                                    <i class="bi bi-heart-pulse"></i>
                                    <div>Saúde</div>
                                </div>
                                <div class="segmento-option" data-segmento="educacao">
                                    <i class="bi bi-book"></i>
                                    <div>Educação</div>
                                </div>
                            </div>
                            <input type="hidden" id="segmento" name="segmento" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="endereco" name="endereco" placeholder="Endereço" required>
                                    <label for="endereco">Endereço</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="cep" name="cep" placeholder="CEP" required>
                                    <label for="cep">CEP</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="cidade" name="cidade" placeholder="Cidade" required>
                                    <label for="cidade">Cidade</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="cep" name="cep" required>
                                        <option value="">Selecione</option>
                                        <option value="AC">Acre</option>
                                        <option value="AL">Alagoas</option>
                                        <option value="AP">Amapá</option>
                                        <option value="AM">Amazonas</option>
                                        <option value="BA">Bahia</option>
                                        <option value="CE">Ceará</option>
                                        <option value="DF">Distrito Federal</option>
                                        <option value="ES">Espírito Santo</option>
                                        <option value="GO">Goiás</option>
                                        <option value="MA">Maranhão</option>
                                        <option value="MT">Mato Grosso</option>
                                        <option value="MS">Mato Grosso do Sul</option>
                                        <option value="MG">Minas Gerais</option>
                                        <option value="PA">Pará</option>
                                        <option value="PB">Paraíba</option>
                                        <option value="PR">Paraná</option>
                                        <option value="PE">Pernambuco</option>
                                        <option value="PI">Piauí</option>
                                        <option value="RJ">Rio de Janeiro</option>
                                        <option value="RN">Rio Grande do Norte</option>
                                        <option value="RS">Rio Grande do Sul</option>
                                        <option value="RO">Rondônia</option>
                                        <option value="RR">Roraima</option>
                                        <option value="SC">Santa Catarina</option>
                                        <option value="SP">São Paulo</option>
                                        <option value="SE">Sergipe</option>
                                        <option value="TO">Tocantins</option>
                                    </select>
                                    <label for="state">Estado</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="prevStep()">
                                <i class="bi bi-arrow-left me-2"></i> Anterior
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextStep()">
                                Próximo <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Passo 3: Dados do Administrador -->
                    <div class="form-section" data-section="3">
                        <h5 class="mb-3">Dados do Administrador</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo" required>
                                    <label for="nome">Nome Completo</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" required>
                                    <label for="email">E-mail</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="senha" name="senha" placeholder="Senha" required minlength="6">
                                    <label for="senha">Senha (mín. 6 caracteres)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirmar Senha" required>
                                    <label for="confirmPassword">Confirmar Senha</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="aceitou_termos" name="aceitou_termos" required>
                            <label class="form-check-label" for="aceitou_termos">
                                Concordo com os <a href="#" class="text-decoration-none">Termos de Uso</a> e 
                                <a href="#" class="text-decoration-none">Política de Privacidade</a>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                            <label class="form-check-label" for="newsletter">
                                Desejo receber novidades e atualizações por e-mail
                            </label>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="prevStep()">
                                <i class="bi bi-arrow-left me-2"></i> Anterior
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i> Criar Conta
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="auth-footer">
                <p class="mb-0">
                    Já tem uma conta? 
                    <a href="index.html" class="text-decoration-none fw-semibold">Faça login</a>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- NexusFlow JS -->
    <script src="assets/js/nexusflow.js"></script>
    
    <script>
        let currentStep = 1;
        
        // Seleção de segmentoo
        document.querySelectorAll('.segmento-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.segmento-option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('segmento').value = this.dataset.segmento;
            });
        });
        
        function nextStep() {
            if (validateCurrentStep()) {
                currentStep++;
                updateStepDisplay();
            }
        }
        
        function prevStep() {
            currentStep--;
            updateStepDisplay();
        }
        
        function updateStepDisplay() {
            // Atualizar indicadores
            document.querySelectorAll('.step').forEach((step, index) => {
                step.classList.remove('active', 'completed');
                if (index + 1 < currentStep) {
                    step.classList.add('completed');
                } else if (index + 1 === currentStep) {
                    step.classList.add('active');
                }
            });
            
            // Atualizar seções
            document.querySelectorAll('.form-section').forEach((section, index) => {
                section.classList.remove('active');
                if (index + 1 === currentStep) {
                    section.classList.add('active');
                }
            });
        }
        
        function validateCurrentStep() {
            const currentSection = document.querySelector(`[data-section="${currentStep}"]`);
            const requiredFields = currentSection.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    nexusFlow.showFieldError(field, 'Este campo é obrigatório');
                    isValid = false;
                } else {
                    nexusFlow.clearFieldError(field);
                }
            });
            
            // Validação específica do passo 2 (segmentoo)
            if (currentStep === 2 && !document.getElementById('segmento').value) {
                nexusFlow.showNotification('Por favor, selecione o segmentoo da sua empresa.', 'warning');
                isValid = false;
            }
            
            // Validação específica do passo 3 (senhas)
            if (currentStep === 3) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                
                if (password.length < 6) {
                    nexusFlow.showFieldError(document.getElementById('password'), 'A senha deve ter pelo menos 6 caracteres');
                    isValid = false;
                }
                
                if (password !== confirmPassword) {
                    nexusFlow.showFieldError(document.getElementById('confirmPassword'), 'As senhas não coincidem');
                    isValid = false;
                }
                
                if (!document.getElementById('terms').checked) {
                    nexusFlow.showNotification('Você deve aceitar os Termos de Uso para continuar.', 'warning');
                    isValid = false;
                }
            }
            
            return isValid;
        }
        
        // Máscaras de entrada
        document.getElementById('cnpj').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/^(\d{2})(\d)/, '$1.$2');
            value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            e.target.value = value;
        });
        
        document.getElementById('cep').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
        
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
        
        // Submit do formulário
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateCurrentStep()) {
                return;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            nexusFlow.setButtonLoading(submitBtn, true);
            
            // Coletar dados do formulário
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            console.log('Dados do cadastro:', data);
            
            // Simular criação da conta
            setTimeout(() => {
                nexusFlow.setButtonLoading(submitBtn, false);
                nexusFlow.showNotification('Conta criada com sucesso! Redirecionando para validação...', 'success');
                
                // Salvar dados temporários para validação
                localStorage.setItem('registrationData', JSON.stringify(data));
                
                setTimeout(() => {
                    window.location.href = 'validacao-email.html';
                }, 2000);
            }, 2000);
        });
    </script>
</body>
</html>
