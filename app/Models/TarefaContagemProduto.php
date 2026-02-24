<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarefaContagemProduto extends Model
{
    protected $table = 'tb_tarefas_contagem_produtos';
    protected $primaryKey = 'id_tarefa_produto';

    protected $fillable = [
        'id_tarefa',
        'id_produto',
        'id_inventario',
        'quantidade_contada',
        'data_contagem',
        'observacao',
    ];

    protected $casts = [
        'quantidade_contada' => 'decimal:2',
        'data_contagem' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function tarefa()
    {
        return $this->belongsTo(TarefaContagem::class, 'id_tarefa');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'id_inventario');
    }
}
