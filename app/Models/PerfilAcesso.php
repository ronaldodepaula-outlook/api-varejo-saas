<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerfilAcesso extends Model
{
    use SoftDeletes;

    protected $table = 'tb_perfis_acesso';
    protected $primaryKey = 'id_perfil';

    protected $fillable = [
        'nome_perfil',
        'descricao',
        'nivel',
        'is_default',
        'is_system',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function permissoes()
    {
        return $this->hasMany(PermissaoPerfil::class, 'id_perfil', 'id_perfil');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'tb_usuarios_perfis', 'id_perfil', 'id_usuario')
            ->withPivot(['data_atribuicao', 'id_usuario_atribuidor', 'data_revogacao', 'motivo_revogacao'])
            ->wherePivotNull('data_revogacao');
    }
}
