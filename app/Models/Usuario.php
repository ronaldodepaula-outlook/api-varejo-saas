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
        'data_criacao'
    ];
    protected $hidden = ['senha'];

    public function empresa() {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
    // Para compatibilidade com o Laravel Auth
    public function getAuthPassword()
    {
        return $this->senha;
    }
}

