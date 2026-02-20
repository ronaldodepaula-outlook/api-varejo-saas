<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtualizacaoPrecoItem extends Model
{
    use HasFactory;

    protected $table = 'tb_atualizacao_precos_itens';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_atualizacao',
        'id_produto',
        'preco_custo_anterior',
        'preco_custo_novo',
        'preco_venda_anterior',
        'preco_venda_novo',
        'status',
        'observacao',
    ];

    protected $casts = [
        'preco_custo_anterior' => 'decimal:2',
        'preco_custo_novo' => 'decimal:2',
        'preco_venda_anterior' => 'decimal:2',
        'preco_venda_novo' => 'decimal:2',
    ];

    public function cabecalho()
    {
        return $this->belongsTo(AtualizacaoPrecoCabecalho::class, 'id_atualizacao');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}
