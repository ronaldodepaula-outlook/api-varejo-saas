<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotacaoCabecalho extends Model
{
    use HasFactory;

    protected $table = 'tb_cotacoes_cabecalho';
    protected $primaryKey = 'id_cotacao';

    protected $fillable = [
        'id_empresa', 'id_filial', 'numero_cotacao', 'descricao', 'data_cotacao', 'data_validade', 'status', 'observacoes', 'id_usuario_criador'
    ];

    public function itens()
    {
        return $this->hasMany(CotacaoItem::class, 'id_cotacao');
    }

    public function fornecedores()
    {
        return $this->hasMany(CotacaoFornecedor::class, 'id_cotacao');
    }

    public function respostas()
    {
        return $this->hasManyThrough(CotacaoResposta::class, CotacaoFornecedor::class, 'id_cotacao', 'id_cotacao_fornecedor', 'id_cotacao', 'id_cotacao_fornecedor');
    }

    public function scopeDaEmpresa($q, $idEmpresa)
    {
        return $q->where('id_empresa', $idEmpresa);
    }
}
