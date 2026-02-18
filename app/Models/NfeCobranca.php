<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfeCobranca extends Model
{
    protected $table = 'tb_nfe_cobranca';
    public $timestamps = false;

    protected $fillable = [
        'id_nfe', 'nFat', 'vOrig', 'vDesc', 'vLiq'
    ];

    public function cabecalho() { return $this->belongsTo(NfeCabecalho::class, 'id_nfe', 'id'); }
    public function duplicatas() { return $this->hasMany(NfeCobrancaDuplicata::class, 'id_cobranca', 'id'); }
}
