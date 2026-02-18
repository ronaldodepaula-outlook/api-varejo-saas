<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdvItemVenda extends Model
{
    use HasFactory;

    protected $table = 'tb_pdv_itens_venda';
    protected $primaryKey = 'id_item_venda';
    public $timestamps = false;

    protected $fillable = [
        'id_venda',
        'id_produto',
        'quantidade',
        'preco_unitario'
    ];

    public function venda()
    {
        return $this->belongsTo(PdvVenda::class, 'id_venda', 'id_venda');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto', 'id_produto');
    }
}
