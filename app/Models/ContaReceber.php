<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContaReceber extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tb_contas_receber';
    protected $primaryKey = 'id_conta_receber';

    protected $fillable = [
        'id_empresa',
        'id_filial',
        'id_orcamento',
        'descricao',
        'valor_total',
        'valor_recebido',
        'data_emissao',
        'data_vencimento',
        'data_recebimento',
        'status',
        'forma_pagamento',
        'parcelas',
        'observacoes',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'valor_recebido' => 'decimal:2',
        'data_emissao' => 'date',
        'data_vencimento' => 'date',
        'data_recebimento' => 'date',
        'parcelas' => 'integer',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'id_filial');
    }
}
