<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfeItensImposto extends Model
{
    protected $table = 'tb_nfe_itens_impostos';
    public $timestamps = false;

    protected $fillable = [
        'id_item', 'vTotTrib', 'orig', 'CSOSN', 'cEnq', 'CST', 'vBC', 'pIPI', 'vIPI', 'vPIS', 'vCOFINS'
    ];

    public function item() { return $this->belongsTo(NfeItens::class, 'id_item', 'id'); }
}
