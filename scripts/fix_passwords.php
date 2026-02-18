<?php
// Script para atualizar todas as senhas de usuários para Bcrypt
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class);

// Bootstrap Eloquent
$app->boot();

use App\Models\Usuario;

// Usar o hasher manualmente
$hasher = new Illuminate\Hashing\BcryptHasher();

foreach (Usuario::all() as $usuario) {
    $senha = $usuario->password;
    // Se não for hash Bcrypt (não começa com $2y$ ou tem menos de 60 caracteres)
    if (strlen($senha) < 60 || substr($senha,0,4)!=='$2y$') {
        $usuario->password = $hasher->make($senha);
        $usuario->save();
        echo "Senha do usuário {$usuario->email} atualizada para Bcrypt.\n";
    }
}
echo "Atualização concluída.\n";
