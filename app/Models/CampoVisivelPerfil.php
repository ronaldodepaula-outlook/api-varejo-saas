<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampoVisivelPerfil extends Model
{
    protected $table = 'tb_campos_visiveis_perfil';
    protected $primaryKey = 'id_campo';
    public $timestamps = true;

    protected $fillable = [
        'id_perfil',
        'tabela',
        'campo',
        'visivel',
        'editavel',
    ];

    protected $casts = [
        'visivel' => 'boolean',
        'editavel' => 'boolean',
    ];

    public function perfil()
    {
        return $this->belongsTo(PerfilAcesso::class, 'id_perfil', 'id_perfil');
    }
}
