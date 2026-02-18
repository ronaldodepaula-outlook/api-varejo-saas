<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfeCobrancaDuplicata extends Model
{
    protected $table = 'tb_nfe_cobranca_duplicatas';
    public $timestamps = false;

    protected $fillable = [
        'id_cobranca', 'nDup', 'dVenc', 'vDup'
    ];

    public function cobranca() { return $this->belongsTo(NfeCobranca::class, 'id_cobranca', 'id'); }
}
