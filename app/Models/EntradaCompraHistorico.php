<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntradaCompraHistorico extends Model
{
    use HasFactory;

    protected $table = 'tb_entradas_compra_historico';
    protected $primaryKey = 'id_historico';
    public $timestamps = false;

    protected $fillable = ['id_entrada','acao','descricao','id_usuario','data_acao'];

    public function entrada()
    {
        return $this->belongsTo(EntradaCompraCabecalho::class, 'id_entrada');
    }
}
