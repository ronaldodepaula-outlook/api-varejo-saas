<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'tb_produtos';
    protected $primaryKey = 'id_produto';

    protected $fillable = [
        'id_empresa',
        'id_categoria',
        'id_secao',
        'id_grupo',
        'id_subgrupo',
        'codigo_barras',
        'descricao',
        'unidade_medida',
        'preco_custo',
        'preco_venda',
        'ativo'
    ];

    // Relacionamentos opcionais
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function secao()
    {
        return $this->belongsTo(Secao::class, 'id_secao');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }

    public function subgrupo()
    {
        return $this->belongsTo(Subgrupo::class, 'id_subgrupo');
    }
}
