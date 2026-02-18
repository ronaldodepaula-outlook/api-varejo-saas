<?php
session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['password'] ?? '';
    if (!$email || !$senha) {
        $error = 'Preencha todos os campos obrigatórios.';
    } else {
        $payload = json_encode(['email' => $email, 'senha' => $senha]);
        $ch = curl_init('http://localhost/saas-multiempresas-api/public/api/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($response, true);
        if ($httpcode === 200 && isset($data['token'], $data['usuario'], $data['empresa'], $data['licenca'])) {
            $_SESSION['authToken'] = $data['token'];
            $_SESSION['usuario'] = $data['usuario'];
            $_SESSION['empresa'] = $data['empresa'];
            $_SESSION['licenca'] = $data['licenca'];
            $_SESSION['userEmail'] = $data['usuario']['email'];
            $_SESSION['userRole'] = $data['usuario']['perfil'];
            header('Location: admin-geral.php');
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
    
    <!-- NexusFlow CSS -->
    <link href="assets/css/nexusflow.css" rel="stylesheet">
    
    <style>
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
        }
        
        .form-floating .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
    </style>
</head>
<body>
    <div class="auth-container d-flex align-items-center justify-content-center p-4">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <h4 class="mb-0">NexusFlow</h4>
                <p class="mb-0 opacity-75">Sistema de Gestão Multi-Empresas</p>
            </div>
            
            <div class="auth-body">
                <form method="post" autocomplete="off">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required autocomplete="username">
                        <label for="email">E-mail</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Senha" required autocomplete="current-password">
                        <label for="password">Senha</label>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label" for="remember">
                                Lembrar-me
                            </label>
                        </div>
                        <a href="#" class="text-decoration-none" onclick="showForgotPassword()">Esqueci minha senha</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Entrar
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
                    <a href="cadastro.html" class="text-decoration-none fw-semibold">Cadastre-se gratuitamente</a>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- NexusFlow JS (opcional, apenas para layout e notificações visuais) -->
</body>
</html>
