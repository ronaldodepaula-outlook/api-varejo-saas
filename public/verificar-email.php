<?php
// Página simples para redirecionar o token para a rota de API de validação
$token = $_GET['token'] ?? basename($_SERVER['REQUEST_URI']);
if (!$token) {
    http_response_code(400);
    echo '<h2>Token de validação não informado.</h2>';
    exit;
}
header('Location: /api/verificar-email/' . urlencode($token));
exit;
