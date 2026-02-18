<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $senha_confirm = $_POST['senha_confirm'] ?? '';

    if ($senha !== $senha_confirm) {
        die('As senhas não conferem.');
    }
    require_once '../../vendor/autoload.php';
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection([
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'NOME_DO_BANCO', // Troque pelo nome do seu banco
        'username'  => 'USUARIO', // Troque pelo usuário do banco
        'password'  => 'SENHA', // Troque pela senha do banco
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ]);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    $tokenRow = $capsule->table('password_reset_tokens')->where('email', $email)->where('token', $token)->first();
    if (!$tokenRow) {
        die('Token inválido ou expirado.');
    }
    $capsule->table('tb_usuarios')->where('email', $email)->update([
        'senha' => password_hash($senha, PASSWORD_DEFAULT)
    ]);
    $capsule->table('password_reset_tokens')->where('email', $email)->delete();
    echo 'Senha redefinida com sucesso!';
    exit;
}
if (!isset($_GET['token']) || !isset($_GET['email'])) {
    die('Link inválido.');
}
$token = $_GET['token'];
$email = $_GET['email'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
</head>
<body>
    <h2>Redefinir Senha</h2>
    <form method="POST" action="reset.php">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <label>Nova Senha:</label><br>
        <input type="password" name="senha" required><br>
        <label>Confirmar Senha:</label><br>
        <input type="password" name="senha_confirm" required><br><br>
        <button type="submit">Redefinir</button>
    </form>
</body>
</html>
