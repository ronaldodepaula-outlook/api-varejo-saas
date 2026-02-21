<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContaPagar extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tb_contas_pagar';
    protected $primaryKey = 'id_conta_pagar';

    protected $fillable = [
        'id_empresa',
        'id_filial',
        'id_fornecedor',
        'descricao',
        'valor_total',
        'valor_pago',
        'data_emissao',
        'data_vencimento',
        'data_pagamento',
        'status',
        'categoria',
        'observacoes',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'valor_pago' => 'decimal:2',
        'data_emissao' => 'date',
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'id_filial');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'id_fornecedor');
    }
}
