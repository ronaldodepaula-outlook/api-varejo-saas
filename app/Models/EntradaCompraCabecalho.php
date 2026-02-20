<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntradaCompraCabecalho extends Model
{
    use HasFactory;

    protected $table = 'tb_entradas_compra_cabecalho';
    protected $primaryKey = 'id_entrada';

    protected $fillable = ['id_empresa','id_filial','id_fornecedor','id_pedido','id_nfe','numero_entrada','data_entrada','data_recebimento','tipo_entrada','valor_total','status','observacoes','id_usuario_recebedor'];

    public function itens()
    {
        return $this->hasMany(EntradaCompraItem::class, 'id_entrada');
    }
}
