<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'tb_grupos';
    protected $primaryKey = 'id_grupo';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_empresa',
        'id_secao',
        'nome_grupo',
        'descricao',
    ];

    public $timestamps = true;

    public function secao()
    {
        return $this->belongsTo(Secao::class, 'id_secao', 'id_secao');
    }

    public function subgrupos()
    {
        return $this->hasMany(Subgrupo::class, 'id_grupo', 'id_grupo');
    }

    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('id_empresa', $empresaId);
    }

    public function scopeDaSecao($query, $secaoId)
    {
        return $query->where('id_secao', $secaoId);
    }
}
