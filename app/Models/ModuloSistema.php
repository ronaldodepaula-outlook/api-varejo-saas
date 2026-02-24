<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuloSistema extends Model
{
    protected $table = 'tb_modulos_sistema';
    protected $primaryKey = 'id_modulo';
    public $timestamps = true;

    protected $fillable = [
        'id_modulo_pai',
        'nome_modulo',
        'descricao',
        'icone',
        'rota',
        'ordem',
        'ativo',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'id_modulo_pai', 'id_modulo');
    }

    public function submodulos()
    {
        return $this->hasMany(self::class, 'id_modulo_pai', 'id_modulo');
    }
}
