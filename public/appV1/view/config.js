// config.js - Arquivo de configuração para URLs da API
const API_CONFIG = {
    BASE_URL: 'https://rdpsolutions.online/saas-multiempresas/public',
    API_VERSION: 'v1',
    
    // Endpoints
    EMPRESAS: '/api/v1/empresas',
    FILIAIS: '/api/v1/filiais',
    LOGIN: '/api/v1/login',
    LOGOUT: '/api/v1/logout',
    
    // Headers padrão (para GET/HEAD) - não inclui Content-Type para evitar preflights desnecessários
    getHeaders: function(token) {
        return {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        };
    },

    // Headers para requisições com corpo JSON (POST/PUT/PATCH)
    getJsonHeaders: function(token) {
        return {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
    },
    
    // URL completa para empresas
    getEmpresasUrl: function() {
        return `${this.BASE_URL}${this.EMPRESAS}`;
    },
    
    // URL completa para uma empresa específica
    getEmpresaUrl: function(id) {
        return `${this.BASE_URL}${this.EMPRESAS}/${id}`;
    },
    
    // URL completa para filiais
    getFiliaisUrl: function() {
        return `${this.BASE_URL}${this.FILIAIS}`;
    },
    
    // URL completa para uma filial específica
    getFilialUrl: function(id) {
        return `${this.BASE_URL}${this.FILIAIS}/${id}`;
    }
};