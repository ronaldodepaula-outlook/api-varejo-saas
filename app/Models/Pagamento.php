<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    protected $table = 'tb_pagamentos';
    protected $primaryKey = 'id_pagamento';
    protected $fillable = [
        'id_assinatura',
        'valor',
        'data_pagamento',
        'metodo',
        'status'
    ];
    public function assinatura()
    {
        return $this->belongsTo(Assinatura::class, 'id_assinatura');
    }
}
