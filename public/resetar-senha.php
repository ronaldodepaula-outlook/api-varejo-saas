<?php
session_start();
$success = '';
$error = '';
$show_form = true;
$token_valid = false;

// Captura token e email da URL se existirem
$token_url = $_GET['token'] ?? '';
$email_url = $_GET['email'] ?? '';

// Função para validar o token com a API
function validarToken($email, $token) {
    $payload = json_encode([
        'token' => $token,
        'email' => $email
    ]);
    
    $ch = curl_init('http://localhost/saas-multiempresas-api/public/api/v1/password/validar-token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Requested-With: XMLHttpRequest'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['httpcode' => $httpcode, 'response' => json_decode($response, true)];
}

// Se temos token e email na URL, validar antes de exibir o formulário
if ($token_url && $email_url) {
    $validacao = validarToken($email_url, $token_url);
    
    if ($validacao['httpcode'] === 200) {
        $token_valid = true;
    } else {
        $token_valid = false;
        $show_form = false;
        if (isset($validacao['response']['erro'])) {
            $error = $validacao['response']['erro'];
        } else {
            $error = 'Token inválido ou expirado.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $token = $_POST['token'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $senha_confirmation = $_POST['senha_confirmation'] ?? '';
    
    // Valida o token novamente antes de processar o POST
    if ($email && $token) {
        $validacao = validarToken($email, $token);
        if ($validacao['httpcode'] !== 200) {
            $token_valid = false;
            $show_form = false;
            if (isset($validacao['response']['erro'])) {
                $error = $validacao['response']['erro'];
            } else {
                $error = 'Token inválido ou expirado.';
            }
        } else {
            $token_valid = true;
        }
    }
    
    if ($token_valid && !$error) {
        if (!$email || !$token || !$senha || !$senha_confirmation) {
            $error = 'Preencha todos os campos obrigatórios.';
        } elseif ($senha !== $senha_confirmation) {
            $error = 'As senhas não coincidem.';
        } elseif (strlen($senha) < 6) {
            $error = 'A senha deve ter no mínimo 6 caracteres.';
        } else {
            $payload = json_encode([
                'email' => $email,
                'token' => $token,
                'senha' => $senha,
                'senha_confirmation' => $senha_confirmation
            ]);
            
            $ch = curl_init('http://localhost/saas-multiempresas-api/public/api/v1/password/resetar-senha');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-Requested-With: XMLHttpRequest'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $data = json_decode($response, true);
            
            if ($httpcode === 200 && isset($data['mensagem'])) {
                $success = $data['mensagem'];
                $show_form = false;
            } elseif ($httpcode === 404 && isset($data['erro'])) {
                $error = $data['erro'];
                $show_form = false;
            } elseif ($httpcode === 403 && isset($data['erro'])) {
                $error = $data['erro'];
                $show_form = false;
            } elseif (isset($data['erro'])) {
                $error = $data['erro'];
            } else {
                $error = 'Erro ao redefinir senha. Tente novamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - NexusFlow</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #2563eb;
            --success-green: #16a34a;
            --error-red: #dc2626;
            --warning-orange: #ea580c;
            --text-gray: #6b7280;
            --light-gray: #f3f4f6;
            --border-light: #e5e7eb;
            --white: #ffffff;
            --transition-fast: 0.2s ease;
        }
        
        body {
            background: var(--light-gray);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-container {
            padding: 20px;
            width: 100%;
            max-width: 400px;
        }
        
        .auth-card {
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-logo {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 0.5rem;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-light);
        }
        
        .status-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
        
        .btn-primary {
            background: var(--primary-blue);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all var(--transition-fast);
        }
        
        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        
        .btn-warning {
            background: var(--warning-orange);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all var(--transition-fast);
        }
        
        .btn-warning:hover {
            background: #c2410c;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
        }
        
        .form-floating .form-control {
            border-radius: 8px;
            border: 1px solid var(--border-light);
            transition: all var(--transition-fast);
        }
        
        .form-floating .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }
        
        .form-floating .form-control:read-only {
            background-color: var(--light-gray);
            opacity: 0.7;
        }
        
        .password-strength {
            height: 4px;
            background: var(--border-light);
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .password-strength.weak .password-strength-bar {
            background: var(--error-red);
            width: 33%;
        }
        
        .password-strength.medium .password-strength-bar {
            background: #f59e0b;
            width: 66%;
        }
        
        .password-strength.strong .password-strength-bar {
            background: var(--success-green);
            width: 100%;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        .alert-success {
            background: #f0fdf4;
            color: var(--success-green);
            border: 1px solid #bbf7d0;
        }
        
        .alert-danger {
            background: #fef2f2;
            color: var(--error-red);
            border: 1px solid #fecaca;
        }
        
        .alert-warning {
            background: #fffbeb;
            color: var(--warning-orange);
            border: 1px solid #fed7aa;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h4 class="mb-2">Redefinir Senha</h4>
                <p class="text-muted mb-0">Crie uma nova senha para sua conta</p>
            </div>
            
            <div class="auth-body">
                <?php if ($success): ?>
                    <!-- Tela de Sucesso -->
                    <div class="status-icon text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="alert alert-success text-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                    <div class="text-center">
                        <a href="app/login.php" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Fazer Login
                        </a>
                    </div>
                
                <?php elseif ($error && !$show_form): ?>
                    <!-- Tela de Token Inválido/Expirado -->
                    <div class="status-icon text-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-muted mb-3">
                            O link de redefinição de senha é inválido ou expirou.
                        </p>
                        
                        <div class="d-grid gap-2">
                            <a href="app/login.php" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Fazer Login
                            </a>
                            <a href="app/esqueci_senha.php" class="btn btn-warning">
                                <i class="bi bi-arrow-repeat me-2"></i>
                                Solicitar Novo Link
                            </a>
                        </div>
                    </div>
                
                <?php else: ?>
                    <!-- Formulário de Redefinição (apenas se token for válido) -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" autocomplete="off" id="resetForm">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="seu@email.com" 
                                   value="<?php echo htmlspecialchars($email_url ?: ($_POST['email'] ?? '')); ?>"
                                   <?php echo $email_url ? 'readonly' : ''; ?> required>
                            <label for="email">E-mail</label>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="token" name="token" 
                                   placeholder="Token de redefinição" 
                                   value="<?php echo htmlspecialchars($token_url ?: ($_POST['token'] ?? '')); ?>"
                                   <?php echo $token_url ? 'readonly' : ''; ?> required>
                            <label for="token">Token de redefinição</label>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="senha" name="senha" 
                                   placeholder="Nova senha" required minlength="6"
                                   oninput="checkPasswordStrength()">
                            <label for="senha">Nova senha</label>
                            <div class="password-strength" id="passwordStrength">
                                <div class="password-strength-bar"></div>
                            </div>
                            <small class="text-muted">Mínimo 6 caracteres</small>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="senha_confirmation" 
                                   name="senha_confirmation" placeholder="Confirme a senha" required
                                   oninput="checkPasswordMatch()">
                            <label for="senha_confirmation">Confirme a senha</label>
                            <div class="mt-1" id="passwordMatch"></div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn">
                            <i class="bi bi-key-fill me-2"></i>
                            Redefinir Senha
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            
            <div class="auth-footer">
                <p class="mb-0 text-muted">
                    <?php if ($show_form && !$success): ?>
                        Lembrou sua senha? 
                        <a href="app/login.php" class="text-decoration-none fw-semibold">Fazer login</a>
                    <?php elseif (!$show_form && !$success && !$error): ?>
                        <a href="app/login.php" class="text-decoration-none fw-semibold">Voltar para o login</a>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('senha').value;
            const strengthBar = document.getElementById('passwordStrength');
            const strengthClasses = ['', 'weak', 'medium', 'strong'];
            
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password) && /[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Limpa classes anteriores
            strengthBar.classList.remove(...strengthClasses);
            
            // Adiciona classe baseada na força
            if (password.length > 0) {
                if (strength <= 2) {
                    strengthBar.classList.add('weak');
                } else if (strength <= 4) {
                    strengthBar.classList.add('medium');
                } else {
                    strengthBar.classList.add('strong');
                }
            }
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('senha').value;
            const confirmPassword = document.getElementById('senha_confirmation').value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirmPassword.length === 0) {
                matchDiv.innerHTML = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchDiv.innerHTML = '<small class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Senhas coincidem</small>';
            } else {
                matchDiv.innerHTML = '<small class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Senhas não coincidem</small>';
            }
        }
        
        // Validação do formulário
        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('senha').value;
            const confirmPassword = document.getElementById('senha_confirmation').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('As senhas não coincidem. Por favor, verifique.');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('A senha deve ter no mínimo 6 caracteres.');
                return false;
            }
            
            // Mostra loading no botão
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Redefinindo...';
        });
    </script>
</body>
</html>