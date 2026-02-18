<?php
session_start();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (!$email) {
        $error = 'Por favor, informe seu e-mail.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, informe um e-mail válido.';
    } else {
        $payload = json_encode(['email' => $email]);
        
    $ch = curl_init('https://rdpsolutions.online/saas-multiempresas/public/api/v1/password/solicitar-reset');
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
        } elseif ($httpcode === 404 && isset($data['erro'])) {
            $error = $data['erro'];
        } elseif ($httpcode === 500 && isset($data['erro'])) {
            $error = 'Erro ao enviar e-mail de recuperação. Tente novamente mais tarde.';
        } elseif (isset($data['erro'])) {
            $error = $data['erro'];
        } else {
            $error = 'Erro inesperado. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - NexusFlow</title>
    
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
        
        .auth-body {
            margin-bottom: 1.5rem;
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
        
        .btn-outline-primary {
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all var(--transition-fast);
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-blue);
            color: var(--white);
            transform: translateY(-1px);
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
        
        .info-box {
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .info-box p {
            margin: 0;
            color: #1e40af;
            font-size: 0.9rem;
        }
        
        .back-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: all var(--transition-fast);
        }
        
        .back-link:hover {
            color: #1d4ed8;
            transform: translateX(-2px);
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
                <h4 class="mb-2">Recuperar Senha</h4>
                <p class="text-muted mb-0">Informe seu e-mail para redefinir a senha</p>
            </div>
            
            <div class="auth-body">
                <?php if ($success): ?>
                    <!-- Tela de Sucesso -->
                    <div class="status-icon text-success text-center">
                        <i class="bi bi-envelope-check"></i>
                    </div>
                    <div class="alert alert-success text-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                    
                    <div class="info-box">
                        <p class="text-center mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Verifique sua caixa de entrada</strong><br>
                            Enviamos um link de recuperação para seu e-mail. O link expira em 60 minutos.
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <a href="login.php" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Voltar para o Login
                        </a>
                        <a href="esqueci_senha.php" class="btn btn-outline-primary w-100">
                            <i class="bi bi-arrow-repeat me-2"></i>
                            Enviar Novo Link
                        </a>
                    </div>
                
                <?php else: ?>
                    <!-- Formulário de Recuperação -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-box">
                        <p>
                            <i class="bi bi-info-circle me-2"></i>
                            Digite o e-mail cadastrado na sua conta. Enviaremos um link para redefinir sua senha.
                        </p>
                    </div>
                    
                    <form method="post" autocomplete="off" id="recoveryForm">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="seu@email.com" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                   required autocomplete="email">
                            <label for="email">
                                <i class="bi bi-envelope me-2"></i>E-mail
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn">
                            <i class="bi bi-send me-2"></i>
                            Enviar Link de Recuperação
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <a href="login.php" class="back-link">
                            <i class="bi bi-arrow-left me-1"></i>
                            Voltar para o login
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (!$success): ?>
            <div class="auth-footer">
                <p class="mb-0 text-muted" style="font-size: 0.875rem;">
                    Não recebeu o e-mail? Verifique sua pasta de spam ou 
                    <a href="esqueci_senha.php" class="text-decoration-none fw-semibold">solicite um novo link</a>.
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Validação do formulário
        document.getElementById('recoveryForm')?.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const submitBtn = document.getElementById('submitBtn');
            
            if (!email) {
                e.preventDefault();
                alert('Por favor, informe seu e-mail.');
                return false;
            }
            
            if (!isValidEmail(email)) {
                e.preventDefault();
                alert('Por favor, informe um e-mail válido.');
                return false;
            }
            
            // Mostra loading no botão
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
        });
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        // Foco no campo de e-mail ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('email');
            if (emailField) {
                emailField.focus();
            }
        });
    </script>
</body>
</html>