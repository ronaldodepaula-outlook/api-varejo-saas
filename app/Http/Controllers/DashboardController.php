<?php
namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Usuario;
use App\Models\Licenca;
use App\Models\Assinatura;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Retorna todos os dados agregados para o dashboard
    public function resumo(Request $request)
    {
        // Empresas
        $empresasCount = Empresa::count();
        $empresasPendentes = Empresa::where('status', 'pendente')->count();
        $empresasPorSegmento = Empresa::select('segmento', DB::raw('count(*) as total'))
            ->groupBy('segmento')->get();

        // Usuários
        $usuariosCount = Usuario::count();
        $usuariosAtivos = Usuario::where('ativo', 1)->count();
        $usuariosInativos = Usuario::where('ativo', 0)->count();
        $usuariosAdmin = Usuario::where('perfil', 'admin_empresa')->count();
        $usuariosSuperAdmin = Usuario::where('perfil', 'super_admin')->count();
        $usuariosNewsletter = Usuario::where('newsletter', 1)->count();

        // Licenças
        $licencasAtivas = Licenca::where('status', 'ativa')->count();
        $licencasExpiradas = Licenca::where('status', 'expirada')->count();
        $licencasCanceladas = Licenca::where('status', 'cancelada')->count();
        $licencasPorPlano = Licenca::select('plano', DB::raw('count(*) as total'))
            ->groupBy('plano')->get();
        $licencasProximasExpiracao = Licenca::where('data_fim', '>=', now()->addDays(1))
            ->where('data_fim', '<=', now()->addDays(30))->count();

        // Assinaturas
        $assinaturasCount = Assinatura::count();
        $assinaturasAtivas = Assinatura::where('status', 'ativa')->count();

        // Pagamentos
        $pagamentosPendentes = Pagamento::where('status', 'pendente')->count();
        $receitaMensal = Pagamento::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'aprovado')
            ->sum('valor');

        // Disponibilidade (simulação)
        $disponibilidade = 98.7;

        // Alertas (simulação)
        $alertas = [
            'licencas_expirando' => Licenca::where('data_fim', '<=', now()->addDays(7))->count(),
            'empresas_pendentes' => $empresasPendentes,
            'pagamentos_pendentes' => $pagamentosPendentes,
            'tentativas_login_suspeitas' => 3, // Simulação
            'backup_status' => 'executado', // Simulação
        ];

        return response()->json([
            'empresas' => [
                'total' => $empresasCount,
                'pendentes' => $empresasPendentes,
                'por_segmento' => $empresasPorSegmento,
            ],
            'usuarios' => [
                'total' => $usuariosCount,
                'ativos' => $usuariosAtivos,
                'inativos' => $usuariosInativos,
                'admin_empresa' => $usuariosAdmin,
                'super_admin' => $usuariosSuperAdmin,
                'newsletter' => $usuariosNewsletter,
            ],
            'licencas' => [
                'ativas' => $licencasAtivas,
                'expiradas' => $licencasExpiradas,
                'canceladas' => $licencasCanceladas,
                'por_plano' => $licencasPorPlano,
                'proximas_expiracao' => $licencasProximasExpiracao,
            ],
            'assinaturas' => [
                'total' => $assinaturasCount,
                'ativas' => $assinaturasAtivas,
            ],
            'pagamentos' => [
                'pendentes' => $pagamentosPendentes,
                'receita_mensal' => $receitaMensal,
            ],
            'disponibilidade' => $disponibilidade,
            'alertas' => $alertas,
        ]);
    }
}
