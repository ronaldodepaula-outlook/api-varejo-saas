// NexusFlow - Sistema SaaS Multi-Empresas
// JavaScript Principal

// Configuração global
const nexusFlow = {
    // Validação de e-mail
    isValidEmail(email) {
        if (!email || typeof email !== 'string') return false;
        // Regex simples para validação de e-mail
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },
    version: '1.0.0',
    apiUrl: '/api/v1',
    
    // Inicialização
    init() {
        this.setupSidebar();
        this.setupNotifications();
        this.setupAuth();
        this.setupTheme();
        this.setupNavigation();
        this.setupForms();
        this.setupTooltips();
        console.log('NexusFlow v' + this.version + ' inicializado com sucesso!');
    },
    
    // Configuração da sidebar
    setupSidebar() {
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                if (mainContent) {
                    mainContent.classList.toggle('sidebar-collapsed');
                }
                
                // Salvar estado da sidebar
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            });
            
            // Restaurar estado da sidebar
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                if (mainContent) {
                    mainContent.classList.add('sidebar-collapsed');
                }
            }
        }
        
        // Configurar navegação ativa
        this.setActiveNavItem();
    },
    
    // Definir item de navegação ativo
    setActiveNavItem() {
        const currentPage = window.location.pathname.split('/').pop() || 'index.html';
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            const href = link.getAttribute('href');
            if (href && href.includes(currentPage)) {
                link.classList.add('active');
            }
        });
    },
    
    // Sistema de notificações
    setupNotifications() {
        // Criar container de notificações se não existir
        if (!document.querySelector('.notifications-container')) {
            const container = document.createElement('div');
            container.className = 'notifications-container position-fixed';
            container.style.cssText = `
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
            `;
            document.body.appendChild(container);
        }
    },
    
    // Mostrar notificação
    showNotification(message, type = 'info', duration = 5000) {
        const container = document.querySelector('.notifications-container');
        const notification = document.createElement('div');
        
        const icons = {
            success: 'bi-check-circle-fill',
            danger: 'bi-exclamation-triangle-fill',
            warning: 'bi-exclamation-circle-fill',
            info: 'bi-info-circle-fill'
        };
        
        const colors = {
            success: 'success',
            danger: 'danger',
            warning: 'warning',
            info: 'primary'
        };
        
        notification.className = `alert alert-${colors[type]} alert-dismissible fade show notification-item mb-2`;
        notification.style.cssText = `
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border: none;
            animation: slideInRight 0.3s ease-out;
        `;
        
        notification.innerHTML = `
            <i class="bi ${icons[type]} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        container.appendChild(notification);
        
        // Auto remover após duração especificada
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }
        }, duration);
    },
    
    // Configuração de autenticação
    setupAuth() {
        // Verificar token em páginas protegidas
        const protectedPages = [
            'admin-geral.html',
            'admin-empresa.html', 
            'usuario.html',
            'gerenciar-empresas.html',
            'gerenciar-usuarios.html',
            'gerenciar-filiais.html',
            'planos-assinaturas.html',
            'perfil.html'
        ];
        
        const currentPage = window.location.pathname.split('/').pop();
        
        if (protectedPages.includes(currentPage)) {
            if (!localStorage.getItem('authToken')) {
                this.showNotification('Sessão expirada. Redirecionando para login...', 'warning');
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 2000);
                return;
            }
            
            // Verificar permissões baseadas no papel
            this.checkPagePermissions(currentPage);
        }
        
        // Configurar logout
        document.addEventListener('click', (e) => {
            if (e.target.dataset.action === 'logout' || e.target.closest('[data-action="logout"]')) {
                e.preventDefault();
                this.logout();
            }
        });
        
        // Auto-logout por inatividade (30 minutos)
        this.setupAutoLogout();
    },
    
    // Verificar permissões da página
    checkPagePermissions(currentPage) {
        const userRole = localStorage.getItem('userRole') || 'usuario';
        
        const pagePermissions = {
            'admin-geral.html': ['super_admin'],
            'planos-assinaturas.html': ['super_admin'],
            'gerenciar-empresas.html': ['super_admin'],
            'admin-empresa.html': ['admin_empresa'],
            'gerenciar-filiais.html': ['admin_empresa', 'super_admin'],
            'usuario.html': ['usuario'],
            'gerenciar-usuarios.html': ['admin_empresa', 'super_admin'],
            'perfil.html': ['super_admin', 'admin_empresa', 'usuario']
        };
        
        const allowedRoles = pagePermissions[currentPage];
        if (allowedRoles && !allowedRoles.includes(userRole)) {
            this.showNotification('Acesso negado para esta página!', 'danger');
            
            // Redirecionar para página apropriada
            const redirectPages = {
                'super_admin': 'admin-geral.html',
                'admin_empresa': 'admin-empresa.html',
                'usuario': 'usuario.html'
            };
            
            setTimeout(() => {
                window.location.href = redirectPages[userRole] || 'index.html';
            }, 2000);
        }
    },
    
    // Configurar auto-logout
    setupAutoLogout() {
        let inactivityTimer;
        const inactivityTime = 30 * 60 * 1000; // 30 minutos
        
        const resetTimer = () => {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                this.showNotification('Sessão expirada por inatividade!', 'warning');
                this.logout();
            }, inactivityTime);
        };
        
        // Eventos que resetam o timer
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'].forEach(event => {
            document.addEventListener(event, resetTimer, true);
        });
        
        resetTimer(); // Iniciar timer
    },
    
    // Função de logout
    logout() {
        localStorage.removeItem('authToken');
        localStorage.removeItem('userRole');
        localStorage.removeItem('userData');
        localStorage.removeItem('companyData');
        
        this.showNotification('Logout realizado com sucesso!', 'success');
        setTimeout(() => {
            window.location.href = 'index.html';
        }, 1000);
    },
    
    // Configuração de navegação
    setupNavigation() {
        // Configurar navegação baseada no papel do usuário
        const userRole = localStorage.getItem('userRole');
        if (userRole) {
            this.configureNavigationByRole(userRole);
        }
        
        // Configurar breadcrumbs dinâmicos
        this.setupBreadcrumbs();
    },
    
    // Configurar navegação por papel
    configureNavigationByRole(userRole) {
        const navSections = document.querySelectorAll('[data-role]');
        
        navSections.forEach(section => {
            const allowedRoles = section.dataset.role.split(',');
            if (!allowedRoles.includes(userRole)) {
                section.style.display = 'none';
            } else {
                section.style.display = 'block';
            }
        });
        
        // Configurar links do dashboard
        const dashboardLinks = document.querySelectorAll('#dashboardLink, #breadcrumbDashboard');
        const dashboardPages = {
            'super_admin': 'admin-geral.html',
            'admin_empresa': 'admin-empresa.html',
            'usuario': 'usuario.html'
        };
        
        dashboardLinks.forEach(link => {
            if (link && dashboardPages[userRole]) {
                link.href = dashboardPages[userRole];
            }
        });
    },
    
    // Configurar breadcrumbs
    setupBreadcrumbs() {
        const currentPage = window.location.pathname.split('/').pop();
        const breadcrumbMap = {
            'admin-geral.html': 'Dashboard Geral',
            'admin-empresa.html': 'Dashboard Empresa',
            'usuario.html': 'Dashboard',
            'gerenciar-empresas.html': 'Gerenciar Empresas',
            'gerenciar-usuarios.html': 'Gerenciar Usuários',
            'gerenciar-filiais.html': 'Gerenciar Filiais',
            'planos-assinaturas.html': 'Planos & Assinaturas',
            'perfil.html': 'Meu Perfil'
        };
        
        const pageTitle = breadcrumbMap[currentPage];
        if (pageTitle) {
            document.title = `${pageTitle} - NexusFlow`;
        }
    },
    
    // Configuração de formulários
    setupForms() {
        // Configurar formulários de login e cadastro
        this.setupAuthForms();
        
        // Validação de formulários
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.showNotification('Por favor, preencha todos os campos obrigatórios!', 'warning');
                }
                form.classList.add('was-validated');
            });
        });
        
        // Máscaras de entrada
        this.setupInputMasks();
        
        // Validação em tempo real
        this.setupRealTimeValidation();
    },
    
    // Configurar formulários de autenticação
    setupAuthForms() {
        // Formulário de login
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleLogin(loginForm);
            });
        }
        
        // Formulário de cadastro
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleRegister(registerForm);
            });
        }
        
        // Formulário de validação de email
        const validationForm = document.getElementById('validationForm');
        if (validationForm) {
            validationForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleEmailValidation(validationForm);
            });
        }
    },
    
    // Manipular login
    handleLogin(form) {
        const formData = new FormData(form);
        const email = formData.get('email');
        const password = formData.get('password');
        
        this.showNotification('Fazendo login...', 'info');
        
        // Simular autenticação
        setTimeout(() => {
            // Determinar papel do usuário baseado no email
            let userRole = 'usuario';
            if (email.includes('admin@') || email.includes('super@')) {
                userRole = 'super_admin';
            } else if (email.includes('empresa@') || email.includes('gerente@')) {
                userRole = 'admin_empresa';
            }
            
            // Salvar dados de autenticação
            localStorage.setItem('authToken', 'mock-jwt-token-' + Date.now());
            localStorage.setItem('userRole', userRole);
            localStorage.setItem('userData', JSON.stringify({
                email: email,
                name: 'Usuário Exemplo',
                role: userRole
            }));
            
            this.showNotification('Login realizado com sucesso!', 'success');
            
            // Redirecionar para dashboard apropriado
            const dashboardPages = {
                'super_admin': 'admin-geral.html',
                'admin_empresa': 'admin-empresa.html',
                'usuario': 'usuario.html'
            };
            
            setTimeout(() => {
                window.location.href = dashboardPages[userRole];
            }, 1000);
        }, 2000);
    },
    
    // Manipular cadastro
    handleRegister(form) {
        const formData = new FormData(form);
        
        this.showNotification('Criando conta...', 'info');
        
        // Simular cadastro
        setTimeout(() => {
            this.showNotification('Conta criada com sucesso! Verifique seu e-mail.', 'success');
            setTimeout(() => {
                window.location.href = 'validacao-email.html';
            }, 1000);
        }, 2000);
    },
    
    // Manipular validação de email
    handleEmailValidation(form) {
        const formData = new FormData(form);
        const code = formData.get('code');
        
        this.showNotification('Validando código...', 'info');
        
        // Simular validação
        setTimeout(() => {
            if (code === '123456') {
                this.showNotification('E-mail validado com sucesso!', 'success');
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 1000);
            } else {
                this.showNotification('Código inválido. Tente novamente.', 'danger');
            }
        }, 1500);
    },
    
    // Configurar máscaras de entrada
    setupInputMasks() {
        // Máscara para telefone
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                    if (value.length < 14) {
                        value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                    }
                }
                e.target.value = value;
            });
        });
        
        // Máscara para CPF/CNPJ
        const docInputs = document.querySelectorAll('input[data-mask="document"]');
        docInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    // CPF
                    value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                } else {
                    // CNPJ
                    value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
                }
                e.target.value = value;
            });
        });
        
        // Máscara para CEP
        const cepInputs = document.querySelectorAll('input[data-mask="cep"]');
        cepInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{5})(\d{3})/, '$1-$2');
                e.target.value = value;
            });
        });
    },
    
    // Validação em tempo real
    setupRealTimeValidation() {
        // Validação de email
        const emailInputs = document.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            input.addEventListener('blur', (e) => {
                const email = e.target.value;
                const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                
                if (email && !isValid) {
                    e.target.classList.add('is-invalid');
                    this.showFieldError(e.target, 'E-mail inválido');
                } else {
                    e.target.classList.remove('is-invalid');
                    this.clearFieldError(e.target);
                }
            });
        });
        
        // Validação de senha
        const passwordInputs = document.querySelectorAll('input[type="password"]');
        passwordInputs.forEach(input => {
            if (input.name === 'password' || input.name === 'newPassword') {
                input.addEventListener('input', (e) => {
                    const password = e.target.value;
                    const strength = this.calculatePasswordStrength(password);
                    this.updatePasswordStrength(input, strength);
                });
            }
        });
    },
    
    // Mostrar erro de campo
    showFieldError(field, message) {
        this.clearFieldError(field);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
    },
    
    // Limpar erro de campo
    clearFieldError(field) {
        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    },
    
    // Calcular força da senha
    calculatePasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        return strength;
    },
    
    // Atualizar indicador de força da senha
    updatePasswordStrength(input, strength) {
        const strengthBar = input.parentNode.querySelector('.password-strength-bar');
        if (strengthBar) {
            const classes = ['weak', 'medium', 'strong', 'very-strong'];
            strengthBar.className = 'password-strength-bar';
            
            if (strength > 0) {
                strengthBar.classList.add(classes[Math.min(strength - 1, 3)]);
            }
        }
    },
    
    // Configurar tooltips
    setupTooltips() {
        // Inicializar tooltips do Bootstrap
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    },
    
    // Configuração de tema
    setupTheme() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        this.setTheme(savedTheme);
        
        // Configurar seletor de tema se existir
        const themeOptions = document.querySelectorAll('[data-theme]');
        themeOptions.forEach(option => {
            option.addEventListener('click', () => {
                const theme = option.dataset.theme;
                this.setTheme(theme);
                
                // Atualizar interface
                themeOptions.forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
            });
            
            // Marcar tema ativo
            if (option.dataset.theme === savedTheme) {
                option.classList.add('active');
            }
        });
    },
    
    // Alterar tema
    setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        
        // Aplicar tema específico se necessário
        if (theme === 'dark') {
            document.body.classList.add('dark-theme');
        } else {
            document.body.classList.remove('dark-theme');
        }
    },
    
    // Utilitários
    utils: {
        // Formatar moeda
        formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(value);
        },
        
        // Formatar data
        formatDate(date, options = {}) {
            const defaultOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            
            return new Intl.DateTimeFormat('pt-BR', { ...defaultOptions, ...options }).format(new Date(date));
        },
        
        // Debounce
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },
        
        // Gerar ID único
        generateId() {
            return Date.now().toString(36) + Math.random().toString(36).substr(2);
        },
        
        // Validar CPF
        validateCPF(cpf) {
            cpf = cpf.replace(/\D/g, '');
            if (cpf.length !== 11) return false;
            
            let sum = 0;
            for (let i = 0; i < 9; i++) {
                sum += parseInt(cpf.charAt(i)) * (10 - i);
            }
            
            let remainder = (sum * 10) % 11;
            if (remainder === 10 || remainder === 11) remainder = 0;
            if (remainder !== parseInt(cpf.charAt(9))) return false;
            
            sum = 0;
            for (let i = 0; i < 10; i++) {
                sum += parseInt(cpf.charAt(i)) * (11 - i);
            }
            
            remainder = (sum * 10) % 11;
            if (remainder === 10 || remainder === 11) remainder = 0;
            
            return remainder === parseInt(cpf.charAt(10));
        },
        
        // Validar CNPJ
        validateCNPJ(cnpj) {
            cnpj = cnpj.replace(/\D/g, '');
            if (cnpj.length !== 14) return false;
            
            const weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
            const weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
            
            let sum = 0;
            for (let i = 0; i < 12; i++) {
                sum += parseInt(cnpj.charAt(i)) * weights1[i];
            }
            
            let remainder = sum % 11;
            const digit1 = remainder < 2 ? 0 : 11 - remainder;
            
            if (digit1 !== parseInt(cnpj.charAt(12))) return false;
            
            sum = 0;
            for (let i = 0; i < 13; i++) {
                sum += parseInt(cnpj.charAt(i)) * weights2[i];
            }
            
            remainder = sum % 11;
            const digit2 = remainder < 2 ? 0 : 11 - remainder;
            
            return digit2 === parseInt(cnpj.charAt(13));
        }
    }
};

// Adicionar animações CSS
const animationStyles = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .notification-item {
        animation: slideInRight 0.3s ease-out;
    }
    
    .sidebar.collapsed {
        width: 70px !important;
    }
    
    .sidebar.collapsed .nav-text,
    .sidebar.collapsed .nav-header-text,
    .sidebar.collapsed .logo-text {
        display: none;
    }
    
    .main-content.sidebar-collapsed {
        margin-left: 70px;
    }
    
    @media (max-width: 768px) {
        .sidebar.collapsed {
            width: 0 !important;
        }
        
        .main-content.sidebar-collapsed {
            margin-left: 0;
        }
    }
`;

// Adicionar estilos ao documento
const styleSheet = document.createElement('style');
styleSheet.textContent = animationStyles;
document.head.appendChild(styleSheet);

// Inicializar quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    nexusFlow.init();
});

// Exportar para uso global
window.nexusFlow = nexusFlow;
