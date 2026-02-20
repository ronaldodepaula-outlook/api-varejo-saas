<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotacaoFornecedor extends Model
{
    use HasFactory;

    protected $table = 'tb_cotacoes_fornecedores';
    protected $primaryKey = 'id_cotacao_fornecedor';

    protected $fillable = ['id_cotacao', 'id_fornecedor', 'data_envio', 'data_resposta', 'prazo_entrega', 'condicoes_pagamento', 'status', 'observacoes'];

    public function cotacao()
    {
        return $this->belongsTo(CotacaoCabecalho::class, 'id_cotacao');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'id_fornecedor');
    }

    public function respostas()
    {
        return $this->hasMany(CotacaoResposta::class, 'id_cotacao_fornecedor');
    }
}
