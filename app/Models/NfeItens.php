<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfeItens extends Model
{
    protected $table = 'tb_nfe_itens';
    public $timestamps = false;

    // Corrigido
    protected $fillable = [
        'id_nfe', 'nItem', 'cProd', 'cEAN', 'xProd', 'NCM', 'CEST', 'CFOP', 'uCom', 'qCom', 'vUnCom', 'vProd',
        'cEANTrib', 'uTrib', 'qTrib', 'vUnTrib', 'indTot', 'xPed', 'nItemPed', 'vTotTrib', 'infAdProd'
    ];

    public function cabecalho() { return $this->belongsTo(NfeCabecalho::class, 'id_nfe', 'id'); }
    public function impostos() { return $this->hasOne(NfeItensImposto::class, 'id_item', 'id'); }
}
