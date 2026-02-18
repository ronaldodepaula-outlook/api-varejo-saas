<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfeEmitente extends Model
{
    protected $table = 'tb_nfe_emitente';
    public $timestamps = false;

    protected $fillable = [
        'id_nfe', 'CNPJ', 'xNome', 'xFant', 'xLgr', 'nro', 'xCpl', 'xBairro',
        'cMun', 'xMun', 'UF', 'CEP', 'cPais', 'xPais', 'fone', 'IE', 'CRT'
    ];

    public function cabecalho() { return $this->belongsTo(NfeCabecalho::class, 'id_nfe', 'id'); }
}
