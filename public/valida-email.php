<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validação de E-mail - NexusFlow</title>
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
            --text-gray: #888;
            --light-gray: #f6f6f6;
        }
        
        body {
            background: var(--light-gray);
            font-family: 'Inter', sans-serif;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-card {
            max-width: 500px;
            width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }
        
        .auth-logo {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 1rem;
        }
        
        .token-box {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 14px;
            color: var(--text-gray);
            word-break: break-all;
            margin: 20px 0;
        }
        
        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            padding: 12px 28px;
            font-weight: 600;
        }
        
        .btn-success {
            background-color: var(--success-green);
            border-color: var(--success-green);
            padding: 12px 28px;
            font-weight: 600;
        }
        
        .success-message {
            color: var(--success-green);
            font-weight: 600;
            margin-top: 20px;
        }
        
        .error-message {
            color: var(--error-red);
            font-weight: 600;
            margin-top: 20px;
        }
        
        .validation-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card text-center">
            <div class="auth-logo">
                <i class="bi bi-diagram-3"></i>
            </div>
            
            <div class="validation-icon text-primary">
                <i class="bi bi-envelope-check"></i>
            </div>
            
            <h2 class="mb-3">Validação de E-mail</h2>
            
            <p class="mb-4">Para validar seu e-mail, clique no botão abaixo.</p>
            
            <div class="token-box" id="token-box">
                <?php 
                // Obtém o token da URL ou do parâmetro GET
                $token = $_GET['token'] ?? basename($_SERVER['REQUEST_URI']);
                echo htmlspecialchars($token); 
                ?>
            </div>
            
            <button class="btn btn-primary mb-3" id="validar-btn">
                <i class="bi bi-check-circle me-2"></i>Validar Agora
            </button>
            
            <div id="resultado" class="mt-3"></div>
            
            <div id="login-container" class="mt-4" style="display: none;">
                <p class="text-muted mb-3">E-mail validado com sucesso!</p>
                <a href="app/login.php" class="btn btn-success">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Fazer Login Agora
                </a>
            </div>
            
            <p class="text-muted mt-4 mb-0" style="font-size: 14px;">
                Se você já validou, pode fechar esta página.
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('validar-btn').addEventListener('click', function(e) {
            e.preventDefault();
            
            const btn = this;
            const originalText = btn.innerHTML;
            const token = document.getElementById('token-box').textContent.trim();
            const resultado = document.getElementById('resultado');
            
            // Desabilita o botão e mostra estado de carregamento
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Validando...';
            
            // Limpa resultados anteriores
            resultado.innerHTML = '';
            
            // Faz a requisição para a API
            fetch('api/verificar-email/' + encodeURIComponent(token))
                .then(function(resp) { 
                    return resp.json(); 
                })
                .then(function(data) {
                    if (data.message) {
                        // Sucesso na validação
                        resultado.innerHTML = '<div class="success-message">' + data.message + '</div>';
                        
                        // Esconde o botão de validação
                        btn.style.display = 'none';
                        
                        // Mostra o botão para login
                        document.getElementById('login-container').style.display = 'block';
                    } else {
                        // Erro inesperado
                        resultado.innerHTML = '<div class="error-message">Erro inesperado. Tente novamente.</div>';
                    }
                })
                .catch(function() {
                    // Erro de conexão
                    resultado.innerHTML = '<div class="error-message">Erro ao conectar à API. Tente novamente mais tarde.</div>';
                })
                .finally(function() {
                    // Restaura o botão
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        });
    </script>
</body>
</html>