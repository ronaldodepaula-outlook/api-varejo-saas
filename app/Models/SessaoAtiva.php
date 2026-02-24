<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessaoAtiva extends Model
{
    protected $table = 'tb_sessoes_ativas';
    protected $primaryKey = 'id_sessao';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'token_sessao',
        'ip_origem',
        'user_agent',
        'data_inicio',
        'ultima_atividade',
        'data_expiracao',
        'ativa',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'ultima_atividade' => 'datetime',
        'data_expiracao' => 'datetime',
        'ativa' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
