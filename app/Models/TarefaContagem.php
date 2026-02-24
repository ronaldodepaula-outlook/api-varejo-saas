<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarefaContagem extends Model
{
    protected $table = 'tb_tarefas_contagem';
    protected $primaryKey = 'id_tarefa';

    protected $fillable = [
        'id_capa_inventario',
        'id_usuario',
        'id_supervisor',
        'tipo_tarefa',
        'data_inicio',
        'data_fim',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function capaInventario()
    {
        return $this->belongsTo(CapaInventario::class, 'id_capa_inventario');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function supervisor()
    {
        return $this->belongsTo(Usuario::class, 'id_supervisor');
    }

    public function historico()
    {
        return $this->hasMany(TarefaContagemHistorico::class, 'id_tarefa');
    }

    public function produtos()
    {
        return $this->hasMany(TarefaContagemProduto::class, 'id_tarefa');
    }
}
