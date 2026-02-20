<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoCompraItem extends Model
{
    use HasFactory;

    protected $table = 'tb_pedidos_compra_itens';
    protected $primaryKey = 'id_item_pedido';

    protected $fillable = ['id_pedido','id_produto','id_resposta_cotacao','quantidade','quantidade_recebida','unidade_medida','preco_unitario','desconto','acrescimo','observacao'];

    public function pedido()
    {
        return $this->belongsTo(PedidoCompraCabecalho::class, 'id_pedido');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}
