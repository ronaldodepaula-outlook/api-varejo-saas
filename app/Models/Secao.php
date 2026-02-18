<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Secao extends Model
{
    protected $table = 'tb_secoes';
    protected $primaryKey = 'id_secao';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_empresa',
        'id_categoria',
        'nome_secao',
        'descricao',
    ];

    public $timestamps = true;

    // Relações
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_secao', 'id_secao');
    }

    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('id_empresa', $empresaId);
    }

    public function scopeDaCategoria($query, $categoriaId)
    {
        return $query->where('id_categoria', $categoriaId);
    }
}
