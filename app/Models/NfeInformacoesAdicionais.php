<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfeInformacoesAdicionais extends Model
{
    protected $table = 'tb_nfe_informacoes_adicionais';
    protected $primaryKey = 'id_info';
    protected $fillable = ['id_nfe', 'infCpl'];
    // DESATIVA timestamps automáticos
     public $timestamps = false;
    public function nfeCabecalho()
    {
        return $this->belongsTo(NfeCabecalho::class, 'id_nfe', 'id_nfe');
    }
}
