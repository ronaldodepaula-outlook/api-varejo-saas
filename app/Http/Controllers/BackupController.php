<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BackupLog;

class BackupController extends Controller
{
    public function executar() {
        // Aqui você pode rodar o comando real de backup (ex: shell_exec, etc)
        // Exemplo: shell_exec('mysqldump ...');
        BackupLog::create(['status' => 'concluído', 'executado_em' => now()]);
        return response()->json(['message' => 'Backup executado com sucesso']);
    }
}
