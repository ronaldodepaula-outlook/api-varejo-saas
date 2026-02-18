<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdvPagamento extends Model
{
    use HasFactory;

    protected $table = 'tb_pdv_pagamentos';
    protected $primaryKey = 'id_pagamento';
    public $timestamps = false;

    protected $fillable = [
        'id_venda',
        'forma_pagamento',
        'valor_pago',
        'valor_troco',
        'data_pagamento'
    ];

    protected $casts = [
        'valor_pago' => 'decimal:2',
        'valor_troco' => 'decimal:2',
        'data_pagamento' => 'datetime'
    ];

    public function venda()
    {
        return $this->belongsTo(PdvVenda::class, 'id_venda', 'id_venda');
    }
}
