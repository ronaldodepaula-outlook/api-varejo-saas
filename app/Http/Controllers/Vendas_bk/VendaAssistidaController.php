<?php
namespace App\Http\Controllers\Vendas;
use App\Models\VendaAssistida;
use App\Models\ItemVendaAssistida;
use Illuminate\Http\Request;


class VendaAssistidaController extends Controller {
public function index() { return VendaAssistida::with('cliente','itens')->get(); }
public function store(Request $r) {
$venda = VendaAssistida::create($r->only(['id_empresa','id_filial','id_cliente','id_usuario','tipo_venda','forma_pagamento','valor_total']));
foreach($r->itens as $item) {
ItemVendaAssistida::create(array_merge($item,['id_venda'=>$venda->id_venda]));
}
return $venda->load('itens');
}
public function show($id) { return VendaAssistida::with('cliente','itens')->findOrFail($id); }
public function update(Request $r, $id) { $v = VendaAssistida::findOrFail($id); $v->update($r->all()); return $v; }
}