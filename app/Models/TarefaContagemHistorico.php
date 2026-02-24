<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarefaContagemHistorico extends Model
{
    protected $table = 'tb_tarefas_contagem_historico';
    protected $primaryKey = 'id_historico';
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'id_tarefa',
        'acao',
        'descricao',
        'id_usuario',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function tarefa()
    {
        return $this->belongsTo(TarefaContagem::class, 'id_tarefa');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
