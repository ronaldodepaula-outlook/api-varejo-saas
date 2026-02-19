<?php
// Equalizar Laravel em ambiente shared (HostGator)
// Uso via navegador: /equalizar.php?key=SUA_CHAVE
// Uso via CLI: php public/equalizar.php

set_time_limit(300);

$base = dirname(__DIR__);
$envFile = $base . DIRECTORY_SEPARATOR . '.env';

function parseEnvFile($path) {
    $vars = [];
    if (!file_exists($path)) {
        return $vars;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, 'export ') === 0) {
            $line = substr($line, 7);
        }
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }
        $key = trim($parts[0]);
        $value = trim($parts[1]);
        if (($value[0] ?? '') === '"' && substr($value, -1) === '"') {
            $value = substr($value, 1, -1);
        } elseif (($value[0] ?? '') === "'" && substr($value, -1) === "'") {
            $value = substr($value, 1, -1);
        }
        if ($key !== '') {
            $vars[$key] = $value;
        }
    }
    return $vars;
}

$envVars = parseEnvFile($envFile);
$requiredKey = $envVars['EQUALIZAR_KEY'] ?? getenv('EQUALIZAR_KEY');
$providedKey = $_GET['key'] ?? '';
$isCli = (PHP_SAPI === 'cli');

if (!$isCli) {
    if (!$requiredKey) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error' => 'Chave nao configurada. Defina EQUALIZAR_KEY no .env e tente novamente.'
        ]);
        exit;
    }
    if (!hash_equals((string) $requiredKey, (string) $providedKey)) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error' => 'Chave invalida.'
        ]);
        exit;
    }
}

require $base . '/vendor/autoload.php';
$app = require $base . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$commands = [
    'config:clear',
    'cache:clear',
    'route:clear',
    'view:clear',
    'optimize:clear',
];

$shouldCache = false;
if ($isCli) {
    $shouldCache = in_array('--cache', $argv ?? [], true);
} else {
    $shouldCache = (isset($_GET['cache']) && $_GET['cache'] === '1');
}
if ($shouldCache) {
    $commands[] = 'config:cache';
}

$results = [];
$hasError = false;

foreach ($commands as $command) {
    try {
        $kernel->call($command);
        $results[$command] = trim($kernel->output());
    } catch (Throwable $e) {
        $hasError = true;
        $results[$command] = 'ERROR: ' . $e->getMessage();
    }
}

header('Content-Type: application/json; charset=utf-8');
http_response_code($hasError ? 500 : 200);
echo json_encode([
    'success' => !$hasError,
    'commands' => $results,
]);
