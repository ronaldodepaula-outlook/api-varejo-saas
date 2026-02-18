<?php
session_start();
$error = '';

// Limpar sessão anterior (para logout)
if (isset($_GET['logout'])) {
    session_destroy();
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['password'] ?? '';
    
    if (!$email || !$senha) {
        $error = 'Preencha todos os campos obrigatórios.';
    } else {
        $payload = json_encode(['email' => $email, 'senha' => $senha]);
    $ch = curl_init('https://rdpsolutions.online/saas-multiempresas/public/api/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($response, true);
        
        if ($httpcode === 200 && isset($data['token'], $data['usuario'], $data['empresa'], $data['licenca'])) {
            // Armazenar TODOS os dados essenciais na sessão
            $_SESSION['authToken'] = $data['token'];
            $_SESSION['usuario'] = $data['usuario'];
            $_SESSION['empresa'] = $data['empresa'];
            $_SESSION['licenca'] = $data['licenca'];
            
            // Dados do usuário
            $_SESSION['user_id'] = $data['usuario']['id_usuario'];
            $_SESSION['user_nome'] = $data['usuario']['nome'];
            $_SESSION['user_email'] = $data['usuario']['email'];
            $_SESSION['user_perfil'] = $data['usuario']['perfil'];
            
            // Dados da empresa
            $_SESSION['empresa_id'] = $data['empresa']['id_empresa'];
            $_SESSION['empresa_nome'] = $data['empresa']['nome_empresa'];
            $_SESSION['empresa_cnpj'] = $data['empresa']['cnpj'];
            $_SESSION['empresa_email'] = $data['empresa']['email_empresa'];
            $_SESSION['empresa_telefone'] = $data['empresa']['telefone'];
            $_SESSION['empresa_website'] = $data['empresa']['website'];
            $_SESSION['empresa_endereco'] = $data['empresa']['endereco'];
            $_SESSION['empresa_cep'] = $data['empresa']['cep'];
            $_SESSION['empresa_cidade'] = $data['empresa']['cidade'];
            $_SESSION['empresa_estado'] = $data['empresa']['estado'];
            $_SESSION['empresa_segmento'] = $data['empresa']['segmento'];
            $_SESSION['empresa_status'] = $data['empresa']['status'];
            $_SESSION['empresa_data_cadastro'] = $data['empresa']['data_cadastro'];
            
            // Dados da licença
            $_SESSION['licenca_id'] = $data['licenca']['id_licenca'];
            $_SESSION['licenca_plano'] = $data['licenca']['plano'];
            $_SESSION['licenca_data_inicio'] = $data['licenca']['data_inicio'];
            $_SESSION['licenca_data_fim'] = $data['licenca']['data_fim'];
            $_SESSION['licenca_status'] = $data['licenca']['status'];
            
            // Timestamps para controle
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();
            
            // Mapeamento de segmentos para views
            $segmento = $_SESSION['empresa_segmento'];
            $viewMap = [
                'admin' => 'home-admins',
                'varejo' => 'home-varejo',
                'ecommerce' => 'home-ecommerce',
                'alimentacao' => 'home-alimentacao',
                'turismo_hotelaria' => 'home-turismo-hotelaria',
                'imobiliario' => 'home-imobiliario',
                'esportes_lazer' => 'home-esportes-lazer',
                'midia_entretenimento' => 'home-midia-entretenimento',
                'industria' => 'home-industria',
                'construcao' => 'home-construcao',
                'agropecuaria' => 'home-agropecuaria',
                'energia_utilities' => 'home-energia-utilities',
                'logistica_transporte' => 'home-logistica-transporte',
                'financeiro' => 'home-financeiro',
                'contabilidade_auditoria' => 'home-contabilidade-auditoria',
                'seguros' => 'home-seguros',
                'marketing' => 'home-marketing',
                'saude' => 'home-saude',
                'educacao' => 'home-educacao',
                'ciencia_pesquisa' => 'home-ciencia-pesquisa',
                'rh_recrutamento' => 'home-rh-recrutamento',
                'juridico' => 'home-juridico',
                'ongs_terceiro_setor' => 'home-ongs-terceiro-setor',
                'seguranca' => 'home-seguranca',
                'outros' => 'home-outros'
            ];
            
            // Determinar a view baseada no segmento
            $targetView = $viewMap[$segmento] ?? 'home-admin';
            
            // Criar parâmetros para redirecionamento
            $params = http_build_query([
                'view' => $targetView,
                'empresa_id' => $_SESSION['empresa_id'],
                'user_id' => $_SESSION['user_id'],
                'segmento' => $segmento,
                'plano' => $_SESSION['licenca_plano']
            ]);
            
            header('Location: index.php?' . $params);
            exit;
            
        } elseif ($httpcode === 403 && isset($data['message'])) {
            $error = 'Usuário inativo ou e-mail não verificado. Por favor, valide sua conta no e-mail.';
        } elseif ($httpcode === 401 && isset($data['message'])) {
            $error = 'Credenciais inválidas.';
        } elseif (isset($data['message'])) {
            $error = $data['message'];
        } else {
            $error = 'Erro ao realizar login. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NexusFlow</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #3498DB;
            --primary-dark: #2C3E50;
            --success: #27AE60;
            --warning: #F39C12;
            --danger: #E74C3C;
            --border-light: #E0E0E0;
            --text-muted: #6C757D;
            --white: #FFFFFF;
            --transition-fast: 0.3s;
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .divider {
            position: relative;
            text-align: center;
            margin: 1.5rem 0;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--border-light);
        }
        
        .divider span {
            background: var(--white);
            padding: 0 1rem;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .btn-login {
            background: var(--primary-blue);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all var(--transition-fast);
        }
        
        .btn-login:hover {
            background: #2980b9;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        
        .form-floating .form-control {
            border-radius: 8px;
            border: 1px solid var(--border-light);
            transition: all var(--transition-fast);
        }
        
        .form-floating .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .auth-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .auth-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 440px;
            position: relative;
            overflow: hidden;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }

        .auth-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 1.8rem;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-header h4 {
            color: var(--primary-dark);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: var(--text-muted);
            margin-bottom: 0;
        }

        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-light);
        }

        .alert-custom {
            border-radius: 8px;
            border: none;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
        }

        .form-check-input:checked {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .btn-outline-secondary {
            border-radius: 8px;
            padding: 0.75rem;
            border: 1px solid var(--border-light);
            color: var(--text-muted);
            transition: all var(--transition-fast);
        }

        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            border-color: var(--primary-blue);
            color: var(--primary-blue);
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            z-index: 10;
        }

        .form-floating {
            position: relative;
        }

        @media (max-width: 576px) {
            .auth-card {
                padding: 2rem 1.5rem;
            }
            
            .auth-logo {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <h4>NexusFlow</h4>
                <p>Sistema de Gestão Multi-Empresas</p>
            </div>
            
            <div class="auth-body">
                <form method="post" autocomplete="off" id="loginForm">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-custom d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="seu@email.com" required autocomplete="username"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        <label for="email">
                            <i class="bi bi-envelope me-2"></i>E-mail
                        </label>
                    </div>
                    
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Senha" required autocomplete="current-password">
                        <label for="password">
                            <i class="bi bi-lock me-2"></i>Senha
                        </label>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Lembrar-me
                            </label>
                        </div>
                        <a href="esqueci_senha.php" class="text-decoration-none text-primary" 
                           onclick="showForgotPassword(event)">
                            Esqueci minha senha
                        </a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login w-100 mb-3" id="loginButton">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        <span id="loginButtonText">Entrar</span>
                        <div class="spinner-border spinner-border-sm ms-2 d-none" id="loginSpinner"></div>
                    </button>
                </form>
                
                <div class="divider">
                    <span>ou</span>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="loginWithGoogle()">
                        <i class="bi bi-google me-2"></i>
                        Entrar com Google
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="loginWithMicrosoft()">
                        <i class="bi bi-microsoft me-2"></i>
                        Entrar com Microsoft
                    </button>
                </div>
            </div>
            
            <div class="auth-footer">
                <p class="mb-0">
                    Não tem uma conta? 
                    <a href="cadastro.php" class="text-decoration-none fw-semibold text-primary">
                        Cadastre-se gratuitamente
                    </a>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });

        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('loginButton');
            const buttonText = document.getElementById('loginButtonText');
            const spinner = document.getElementById('loginSpinner');
            
            button.disabled = true;
            buttonText.textContent = 'Entrando...';
            spinner.classList.remove('d-none');
        });

        function loginWithGoogle() {
            // Implementar integração com Google OAuth
            alert('Integração com Google em desenvolvimento');
        }

        function loginWithMicrosoft() {
            // Implementar integração com Microsoft OAuth
            alert('Integração com Microsoft em desenvolvimento');
        }

        function showForgotPassword(event) {
            event.preventDefault();
            // Implementar modal ou redirecionamento para recuperação de senha
            alert('Redirecionando para recuperação de senha');
        }

        // Auto-focus no campo de email
        document.getElementById('email').focus();

        // Prevenir reenvio do formulário ao recarregar a página
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>