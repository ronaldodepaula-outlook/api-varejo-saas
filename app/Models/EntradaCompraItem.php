<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntradaCompraItem extends Model
{
    use HasFactory;

    protected $table = 'tb_entradas_compra_itens';
    protected $primaryKey = 'id_item_entrada';

    protected $fillable = ['id_entrada','id_produto','id_item_pedido','quantidade_recebida','quantidade_conferida','unidade_medida','preco_unitario','lote','data_fabricacao','data_validade','observacao'];

    public function entrada()
    {
        return $this->belongsTo(EntradaCompraCabecalho::class, 'id_entrada');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}
