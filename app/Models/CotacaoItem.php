<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotacaoItem extends Model
{
    use HasFactory;

    protected $table = 'tb_cotacoes_itens';
    protected $primaryKey = 'id_item_cotacao';

    protected $fillable = ['id_cotacao', 'id_produto', 'quantidade', 'unidade_medida', 'observacao'];

    public function cotacao()
    {
        return $this->belongsTo(CotacaoCabecalho::class, 'id_cotacao');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}
