<?php
namespace App\Http\Controllers;
use App\Models\Licenca;
use App\Models\Empresa;
use App\Models\Usuario;
use App\Models\Filial;
use App\Models\VerificacaoEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\PHPMailerHelper;
use Illuminate\Support\Str;

class CompanyUserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'empresa.nome_empresa' => 'required|string|max:150',
            'empresa.cnpj' => 'nullable|string|max:20',
            'empresa.email_empresa' => 'nullable|email',
            'empresa.telefone' => 'nullable|string|max:20',
            'empresa.website' => 'nullable|string|max:150',
            'empresa.endereco' => 'nullable|string|max:255',
            'empresa.cep' => 'nullable|string|max:10',
            'empresa.cidade' => 'nullable|string|max:100',
            'empresa.estado' => 'nullable|string|max:2',
            'empresa.segmento' => 'nullable|in:varejo,ecommerce,alimentacao,turismo_hotelaria,imobiliario,esportes_lazer,midia_entretenimento,industria,construcao,agropecuaria,energia_utilities,logistica_transporte,financeiro,contabilidade_auditoria,seguros,marketing,saude,educacao,ciencia_pesquisa,rh_recrutamento,juridico,ongs_terceiro_setor,seguranca,outros',
            'usuario.nome' => 'required|string|max:100',
            'usuario.email' => 'required|email',
            'usuario.senha' => 'required|min:6',
            'usuario.aceitou_termos' => 'nullable|boolean',
            'usuario.newsletter' => 'nullable|boolean',
        ]);

        $empresaExistente = Empresa::where('cnpj', $request->input('empresa.cnpj'))->first();
        $usuarioExistente = Usuario::where('email', $request->input('usuario.email'))->first();

        if ($empresaExistente && $usuarioExistente) {
            return response()->json([
                'message' => 'Usuário e empresa já cadastrados',
                'empresa' => $empresaExistente,
                'usuario' => $usuarioExistente
            ], 409);
        }
        if ($empresaExistente) {
            return response()->json([
                'message' => 'Empresa com o CNPJ já cadastrada, com usuário do email ' . $usuarioExistente?->email,
                'empresa' => $empresaExistente
            ], 409);
        }
        if ($usuarioExistente) {
            $empresaUsuario = $usuarioExistente->empresa;
            return response()->json([
                'message' => 'Usuário já cadastrado com o email informado associado à empresa ' . ($empresaUsuario ? $empresaUsuario->nome_empresa : ''),
                'usuario' => $usuarioExistente
            ], 409);
        }

        // Inicia transação para garantir consistência dos dados
        try {
            \DB::beginTransaction();

            // Cria empresa
            $empresa = Empresa::create([
                'nome_empresa' => $request->input('empresa.nome_empresa'),
                'cnpj' => $request->input('empresa.cnpj'),
                'email_empresa' => $request->input('empresa.email_empresa'),
                'telefone' => $request->input('empresa.telefone'),
                'website' => $request->input('empresa.website'),
                'endereco' => $request->input('empresa.endereco'),
                'cep' => $request->input('empresa.cep'),
                'cidade' => $request->input('empresa.cidade'),
                'estado' => $request->input('empresa.estado'),
                'segmento' => $request->input('empresa.segmento'),
                'status' => 'pendente',
            ]);

            // Cria filial matriz automaticamente
            $filialMatriz = Filial::create([
                'id_empresa' => $empresa->id_empresa,
                'nome_filial' => 'MATRIZ - ' . $request->input('empresa.nome_empresa'),
                'endereco' => $request->input('empresa.endereco'),
                'cidade' => $request->input('empresa.cidade'),
                'estado' => $request->input('empresa.estado'),
                'cep' => $request->input('empresa.cep'),
                'data_cadastro' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Cria usuário associado à empresa
            $usuario = Usuario::create([
                'id_empresa' => $empresa->id_empresa,
                'nome' => $request->input('usuario.nome'),
                'email' => $request->input('usuario.email'),
                'senha' => Hash::make($request->input('usuario.senha')),
                'perfil' => 'admin_empresa',
                'ativo' => 0,
                'aceitou_termos' => $request->input('usuario.aceitou_termos', 0),
                'newsletter' => $request->input('usuario.newsletter', 0),
            ]);

            // Cria licença trial de 3 meses
            $dataInicio = now()->toDateString();
            $dataFim = now()->addMonths(3)->toDateString();
            $licenca = Licenca::create([
                'id_empresa' => $empresa->id_empresa,
                'plano' => 'trial',
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'status' => 'ativa'
            ]);

            // Cria token de verificação
            $token = Str::random(60);
            VerificacaoEmail::create([
                'id_usuario' => $usuario->id_usuario,
                'token' => $token
            ]);

            \DB::commit();

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'message' => 'Erro ao cadastrar empresa e usuário: ' . $e->getMessage()
            ], 500);
        }

        // Envia e-mail de verificação (fora da transação)
        $link = url("/valida-email.php?token={$token}");
        $assunto = 'Validação de Conta - SaaS MultiEmpresas';
        $html = '<!DOCTYPE html>' .
            '<html lang="pt-br">'
            .'<head>'
            .'<meta charset="UTF-8">'
            .'<title>Validação de Conta</title>'
            .'</head>'
            .'<body style="font-family: Arial, sans-serif; background: #f6f6f6; padding: 40px;">'
            .'<div style="max-width: 480px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; padding: 32px 24px;">'
            .'<h2 style="color: #2d3748;">Bem-vindo ao SaaS MultiEmpresas!</h2>'
            .'<p style="color: #444; font-size: 16px;">Olá <b>' . htmlspecialchars($usuario->nome) . '</b>,</p>'
            .'<p style="color: #444; font-size: 16px;">Para ativar sua conta, clique no botão abaixo:</p>'
            .'<div style="text-align: center; margin: 32px 0;">'
            .'<a href="' . $link . '" style="background: #2563eb; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 18px; font-weight: bold; display: inline-block;">Validar Conta</a>'
            .'</div>'
            .'<p style="color: #888; font-size: 13px;">Se não foi você que solicitou o cadastro, ignore este e-mail.</p>'
            .'<hr style="margin: 32px 0; border: none; border-top: 1px solid #eee;">'
            .'<p style="color: #aaa; font-size: 12px; text-align: center;">&copy; '.date('Y').' SaaS MultiEmpresas</p>'
            .'</div>'
            .'</body></html>';
        $texto = "Olá {$usuario->nome},\n\nPara ativar sua conta, acesse: $link\n\nSe não foi você que solicitou o cadastro, ignore este e-mail.";
        
        try {
            PHPMailerHelper::send(
                $usuario->email,
                $assunto,
                $html,
                $texto
            );
        } catch (\Exception $e) {
            // Log do erro de e-mail, mas não falha o cadastro
            \Log::error('Erro ao enviar e-mail de verificação: ' . $e->getMessage());
        }

        return response()->json([
            'empresa' => $empresa,
            'usuario' => $usuario,
            'licenca' => $licenca,
            'filial_matriz' => $filialMatriz,
            'message' => 'Empresa, usuário, licença trial e filial matriz cadastrados. Verifique o e-mail para ativar a conta.'
        ], 201);
    }
}