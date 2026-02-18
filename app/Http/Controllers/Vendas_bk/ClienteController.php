<?php
namespace App\Http\Controllers\Vendas;
use App\Models\Cliente;
use Illuminate\Http\Request;


class ClienteController extends Controller {
public function index() { return Cliente::where('flag_excluido_logico',0)->get(); }
public function store(Request $r) { return Cliente::create($r->all()); }
public function show($id) { return Cliente::findOrFail($id); }
public function update(Request $r, $id) { $c = Cliente::findOrFail($id); $c->update($r->all()); return $c; }
public function destroy($id) { $c = Cliente::findOrFail($id); $c->delete(); return response()->json(['deleted'=>true]); }
}