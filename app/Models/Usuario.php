<?php

// app/Models/Usuario.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Usuario extends Authenticatable {
    use HasApiTokens, Notifiable;

    protected $table = 'tb_usuarios';
    protected $primaryKey = 'id_usuario';
    protected $fillable = [
        'id_empresa',
        'nome',
        'email',
        'senha',
        'perfil',
        'ativo',
        'aceitou_termos',
        'newsletter',
        'email_verificado_em',
        'data_criacao',
        'ultimo_login',
        'ultimo_ip',
        'tentativas_login',
        'bloqueado_ate',
        'dois_fatores_ativo',
        'segredo_2fa',
        'token_recuperacao',
        'token_expiracao',
        'sessoes_ativas'
    ];
    protected $hidden = ['senha'];

    public function empresa() {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function perfis()
    {
        return $this->belongsToMany(PerfilAcesso::class, 'tb_usuarios_perfis', 'id_usuario', 'id_perfil')
            ->withPivot(['data_atribuicao', 'id_usuario_atribuidor', 'data_revogacao', 'motivo_revogacao'])
            ->wherePivotNull('data_revogacao');
    }

    public function permissoesEspeciais()
    {
        return $this->hasMany(PermissaoUsuario::class, 'id_usuario', 'id_usuario');
    }

    public function restricoesFiliais()
    {
        return $this->hasMany(RestricaoFilialUsuario::class, 'id_usuario', 'id_usuario');
    }

    public function sessoesAtivas()
    {
        return $this->hasMany(SessaoAtiva::class, 'id_usuario', 'id_usuario')
            ->where('ativa', true);
    }

    public function logs()
    {
        return $this->hasMany(LogAcaoUsuario::class, 'id_usuario', 'id_usuario');
    }

    public function nivelMaximo(): int
    {
        $nivel = $this->perfis()->max('nivel');
        return $nivel ? (int) $nivel : 0;
    }

    public function isAdmin(): bool
    {
        return $this->perfis()->where('nivel', '>=', 900)->exists();
    }
    // Para compatibilidade com o Laravel Auth
    public function getAuthPassword()
    {
        return $this->senha;
    }
}
