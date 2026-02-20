<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotacaoResposta extends Model
{
    use HasFactory;

    protected $table = 'tb_cotacoes_respostas';
    protected $primaryKey = 'id_resposta';

    protected $fillable = ['id_cotacao_fornecedor', 'id_produto', 'quantidade', 'preco_unitario', 'prazo_entrega_item', 'observacao', 'selecionado'];

    public function cotacaoFornecedor()
    {
        return $this->belongsTo(CotacaoFornecedor::class, 'id_cotacao_fornecedor');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}
