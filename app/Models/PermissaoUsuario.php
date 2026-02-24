<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissaoUsuario extends Model
{
    protected $table = 'tb_permissoes_usuario';
    protected $primaryKey = 'id_permissao_usuario';
    public $timestamps = true;

    protected $fillable = [
        'id_usuario',
        'id_modulo',
        'id_acao',
        'permitido',
    ];

    protected $casts = [
        'permitido' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
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
