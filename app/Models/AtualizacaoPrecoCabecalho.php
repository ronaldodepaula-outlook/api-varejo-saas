<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtualizacaoPrecoCabecalho extends Model
{
    use HasFactory;

    protected $table = 'tb_atualizacao_precos_cabecalho';
    protected $primaryKey = 'id_atualizacao';

    protected $fillable = [
        'id_empresa',
        'id_filial',
        'numero_lote',
        'descricao',
        'tipo_atualizacao',
        'id_fornecedor',
        'data_atualizacao',
        'status',
        'observacoes',
        'id_usuario_criador',
        'processed_at',
    ];

    protected $casts = [
        'data_atualizacao' => 'date',
        'processed_at' => 'datetime',
    ];

    public function itens()
    {
        return $this->hasMany(AtualizacaoPrecoItem::class, 'id_atualizacao');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'id_fornecedor');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'id_filial');
    }

    public function usuarioCriador()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_criador');
    }
}
