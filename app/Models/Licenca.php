<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licenca extends Model
{
    protected $table = 'tb_licencas';
    protected $primaryKey = 'id_licenca';
    protected $fillable = [
        'id_empresa',
        'plano',
        'data_inicio',
        'data_fim',
        'status'
    ];
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
