// config.js - Arquivo de configuração para URLs da API
const API_CONFIG = {
    BASE_URL: 'http://localhost/saas-multiempresas-api/public',
    API_VERSION: 'v1',
    
    // Endpoints
    EMPRESAS: '/api/v1/empresas',
    FILIAIS: '/api/v1/filiais',
    LOGIN: '/api/v1/login',
    LOGOUT: '/api/v1/logout',
    
    // Headers padrão
    getHeaders: function(token) {
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