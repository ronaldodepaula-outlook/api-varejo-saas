<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdvFechamentoPendente extends Model
{
    use HasFactory;

    protected $table = 'tb_pdv_fechamentos_pendentes';
    protected $fillable = [
        'id_caixa',
        'id_empresa',
        'valor_fechamento',
        'status',
        'meta',
        'created_by'
    ];

    protected $casts = [
        'meta' => 'array',
        'valor_fechamento' => 'decimal:2'
    ];
}
