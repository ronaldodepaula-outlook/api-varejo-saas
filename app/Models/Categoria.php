<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'tb_categorias';
    protected $primaryKey = 'id_categoria';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_empresa',
        'nome_categoria',
        'descricao',
    ];

    public $timestamps = true;

    // Relações
    public function secoes()
    {
        return $this->hasMany(Secao::class, 'id_categoria', 'id_categoria');
    }

    public function produtos()
    {
        // caso deseje acessar produtos vinculados
        return $this->hasMany(\App\Models\Produto::class, 'id_categoria', 'id_categoria');
    }

    // Scope por empresa
    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('id_empresa', $empresaId);
    }
}
