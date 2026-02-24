<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioPerfil extends Model
{
    protected $table = 'tb_usuarios_perfis';
    protected $primaryKey = 'id_usuario_perfil';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_perfil',
        'data_atribuicao',
        'id_usuario_atribuidor',
        'data_revogacao',
        'motivo_revogacao',
    ];

    protected $casts = [
        'data_atribuicao' => 'datetime',
        'data_revogacao' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function perfil()
    {
        return $this->belongsTo(PerfilAcesso::class, 'id_perfil', 'id_perfil');
    }
}
