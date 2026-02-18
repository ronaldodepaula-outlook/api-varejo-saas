<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfeDestinatario extends Model
{
    protected $table = 'tb_nfe_destinatario';
    public $timestamps = false;

    protected $fillable = [
        'id_nfe', 'CNPJ', 'xNome', 'xLgr', 'nro', 'xBairro', 'cMun', 'xMun', 'UF',
        'CEP', 'cPais', 'xPais', 'fone', 'indIEDest', 'IE', 'email'
    ];

    public function cabecalho() { 
        return $this->belongsTo(NfeCabecalho::class, 'id_nfe', 'id_nfe'); 
    }
}