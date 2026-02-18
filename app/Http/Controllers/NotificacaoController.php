<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Licenca;
use App\Models\LoginAttempt;
use App\Models\BackupLog;

class NotificacaoController extends Controller
{
    public function licencasExpirando() {
        $count = Licenca::where('data_fim', '>=', now())
            ->where('data_fim', '<=', now()->addDays(30))
            ->where('status', 'ativa')
            ->count();
        return response()->json(['licencas_expirando' => $count]);
    }

    public function tentativasLoginSuspeitas() {
        $count = LoginAttempt::where('success', false)
            ->where('attempted_at', '>=', now()->subDay())
            ->count();
        return response()->json(['tentativas_suspeitas' => $count]);
    }

    public function backupStatus() {
        $backup = BackupLog::latest('executado_em')->first();
        return response()->json([
            'status' => $backup ? $backup->status : 'indisponível',
            'executado_em' => $backup?->executado_em
        ]);
    }

    public function versaoSistema() {
        return response()->json([
            'versao' => '2.1.3',
            'novidades' => ['Novas funcionalidades disponíveis']
        ]);
    }
}
