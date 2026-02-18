<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DebitoCliente extends Model {
    protected $table = 'tb_debitos_clientes';
    protected $primaryKey = 'id_debito';
    public $timestamps = false;
    protected $fillable = ['id_cliente','pseudonymous_customer_id','id_venda','valor','status','observacao','data_geracao','data_pagamento'];
    public function cliente() { return $this->belongsTo(Cliente::class,'id_cliente','id_cliente'); }
    public function venda() { return $this->belongsTo(VendaAssistida::class,'id_venda','id_venda'); }
}
