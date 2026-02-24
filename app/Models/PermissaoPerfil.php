<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissaoPerfil extends Model
{
    protected $table = 'tb_permissoes_perfil';
    protected $primaryKey = 'id_permissao';
    public $timestamps = true;

    protected $fillable = [
        'id_perfil',
        'id_modulo',
        'id_acao',
        'permitido',
    ];

    protected $casts = [
        'permitido' => 'boolean',
    ];

    public function perfil()
    {
        return $this->belongsTo(PerfilAcesso::class, 'id_perfil', 'id_perfil');
    }

    public function modulo()
    {
        return $this->belongsTo(ModuloSistema::class, 'id_modulo', 'id_modulo');
    }

    public function acao()
    {
        return $this->belongsTo(PermissaoAcao::class, 'id_acao', 'id_acao');
    }
}
