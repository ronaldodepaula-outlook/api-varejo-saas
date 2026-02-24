<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestricaoFilialUsuario extends Model
{
    protected $table = 'tb_restricoes_filiais_usuario';
    protected $primaryKey = 'id_restricao';
    public $timestamps = true;

    protected $fillable = [
        'id_usuario',
        'id_filial',
        'pode_acessar',
    ];

    protected $casts = [
        'pode_acessar' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'id_filial', 'id_filial');
    }
}
