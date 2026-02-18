<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$usuario = $_SESSION['usuario'] ?? [];
$empresa = $_SESSION['empresa'] ?? [];
$licenca = $_SESSION['licenca'] ?? [];
$token = $_SESSION['authToken'] ?? '';
echo $segmento = $_SESSION['segmento'] ?? '';

function arrayToTable($arr) {
    if (!is_array($arr) || empty($arr)) return '<em>Nenhum dado</em>';
    $html = '<table class="table table-bordered table-striped table-sm"><tbody>';
    foreach ($arr as $k => $v) {
        $html .= '<tr><th>' . htmlspecialchars($k) . '</th><td>';
        if (is_array($v)) {
            $html .= '<ol class="mb-0">';
            foreach ($v as $subk => $subv) {
                $html .= '<li><strong>' . htmlspecialchars($subk) . ':</strong> ' . htmlspecialchars(is_array($subv) ? json_encode($subv, JSON_UNESCAPED_UNICODE) : $subv) . '</li>';
            }
            $html .= '</ol>';
        } else {
            $html .= htmlspecialchars($v);
        }
        $html .= '</td></tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Debug Sessão - NexusFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">Parâmetros da Sessão (Debug)</h2>
        <div class="card mb-3">
            <div class="card-header">Usuário</div>
            <div class="card-body">
                <?php echo arrayToTable($usuario); ?>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">Empresa</div>
            <div class="card-body">
                <?php echo arrayToTable($empresa); ?>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">Licença</div>
            <div class="card-body">
                <?php echo arrayToTable($licenca); ?>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">Token</div>
            <div class="card-body">
                <ol><li><?php echo htmlspecialchars($token); ?></li></ol>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">Segmento</div>
            <div class="card-body">
                <ol><li><?php echo htmlspecialchars($segmento); ?></li></ol>
            </div>
        </div>
    </div>
</body>
</html>
