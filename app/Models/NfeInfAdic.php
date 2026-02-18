<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfeInfAdic extends Model
{
    protected $table = 'tb_nfe_informacoes_adicionais';
    public $timestamps = false;

    protected $fillable = [
        'id_nfe', 'infCpl'
    ];

    public function cabecalho() { return $this->belongsTo(NfeCabecalho::class, 'id_nfe', 'id'); }
}
