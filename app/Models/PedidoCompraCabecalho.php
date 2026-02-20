<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoCompraCabecalho extends Model
{
    use HasFactory;

    protected $table = 'tb_pedidos_compra_cabecalho';
    protected $primaryKey = 'id_pedido';

    protected $fillable = ['id_empresa','id_filial','id_fornecedor','id_cotacao','numero_pedido','data_pedido','data_previsao_entrega','condicoes_pagamento','valor_total','status','observacoes','id_usuario_criador','id_usuario_aprovador','data_aprovacao'];

    public function itens()
    {
        return $this->hasMany(PedidoCompraItem::class, 'id_pedido');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'id_fornecedor');
    }
}
