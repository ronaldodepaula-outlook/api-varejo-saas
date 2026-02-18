<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subgrupo extends Model
{
    protected $table = 'tb_subgrupos';
    protected $primaryKey = 'id_subgrupo';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_empresa',
        'id_grupo',
        'nome_subgrupo',
        'descricao',
    ];

    public $timestamps = true;

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id_grupo');
    }

    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('id_empresa', $empresaId);
    }

    public function scopeDoGrupo($query, $grupoId)
    {
        return $query->where('id_grupo', $grupoId);
    }
}
