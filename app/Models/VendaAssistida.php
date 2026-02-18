<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VendaAssistida extends Model {
    protected $table = 'tb_vendas_assistidas';
    protected $primaryKey = 'id_venda';
    public $timestamps = false;
    protected $fillable = [
        'id_empresa','id_filial','id_cliente','pseudonymous_customer_id','id_usuario',
        'tipo_venda','forma_pagamento','status','valor_total','observacao','data_fechamento'
    ];

    public function cliente() { return $this->belongsTo(Cliente::class,'id_cliente','id_cliente'); }
    public function itens() { return $this->hasMany(ItemVendaAssistida::class,'id_venda','id_venda'); }
}
