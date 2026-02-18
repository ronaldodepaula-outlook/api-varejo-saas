<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfeTransporte extends Model
{
    protected $table = 'tb_nfe_transporte';
    public $timestamps = false;

    protected $fillable = [
        'id_nfe', 'modFrete', 'CNPJ', 'xNome', 'IE', 'xEnder', 'xMun', 'UF', 'esp', 'pesoL', 'pesoB'
    ];

    public function cabecalho() { return $this->belongsTo(NfeCabecalho::class, 'id_nfe', 'id'); }
}
