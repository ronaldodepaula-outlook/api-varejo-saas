<?php
// Configurações globais do sistema
return [
    // URL base do sistema
    'url_base' => 'http://localhost/saas-multiempresas-api/public/app/',

    // Nome do sistema
    'nome_sistema' => 'NexusFlow',

    // Configurações de banco de dados
    'db' => [
        'host' => 'localhost',
        'dbname' => 'saas_multiempresas',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],

    // Configurações de e-mail
    'email' => [
        'from_name' => 'NexusFlow Suporte',
        'from_email' => 'suporte@nexusflow.com',
        'smtp_host' => 'smtp.nexusflow.com',
        'smtp_port' => 587,
        'smtp_user' => 'suporte@nexusflow.com',
        'smtp_pass' => 'SENHA_AQUI',
        'smtp_secure' => 'tls',
    ],

    // Outras configurações
    'versao' => '1.0.0',
    'idioma' => 'pt-BR',
    'timezone' => 'America/Sao_Paulo',
];
