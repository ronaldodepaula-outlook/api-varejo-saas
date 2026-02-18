<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ItemVendaAssistida extends Model {
    protected $table = 'tb_itens_venda_assistida';
    protected $primaryKey = 'id_item_venda';
    public $timestamps = false;
    // incluir id_empresa e id_filial pois a tabela exige essas colunas
    protected $fillable = ['id_venda','id_empresa','id_filial','id_produto','quantidade','valor_unitario'];

    public function venda() { return $this->belongsTo(VendaAssistida::class,'id_venda','id_venda'); }
}
