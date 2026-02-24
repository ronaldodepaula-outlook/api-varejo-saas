<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAcaoUsuario extends Model
{
    protected $table = 'tb_log_acoes_usuarios';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_empresa',
        'acao',
        'modulo',
        'tabela',
        'id_registro',
        'dados_anteriores',
        'dados_novos',
        'ip_origem',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }
}
