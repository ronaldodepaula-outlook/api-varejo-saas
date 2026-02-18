<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfePagamentos extends Model
{
    protected $table = 'tb_nfe_pagamentos';
    public $timestamps = false;

    protected $fillable = [
        'id_nfe', 'indPag', 'tPag', 'vPag'
    ];

    public function cabecalho() { return $this->belongsTo(NfeCabecalho::class, 'id_nfe', 'id'); }
}
