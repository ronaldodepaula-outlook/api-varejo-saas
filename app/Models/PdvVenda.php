<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdvVenda extends Model
{
    use HasFactory;

    protected $table = 'tb_pdv_vendas';
    protected $primaryKey = 'id_venda';
    public $timestamps = false;

    protected $fillable = [
        'id_caixa',
        'id_empresa',
        'id_filial',
        'id_cliente',
        'valor_total',
        'tipo_venda',
        'data_venda',
        'status'
    ];

    public function caixa()
    {
        return $this->belongsTo(PdvCaixa::class, 'id_caixa', 'id_caixa');
    }

    public function itens()
    {
        return $this->hasMany(PdvItemVenda::class, 'id_venda', 'id_venda');
    }

    public function pagamentos()
    {
        return $this->hasMany(PdvPagamento::class, 'id_venda', 'id_venda');
    }
}
