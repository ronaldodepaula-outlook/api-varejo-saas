<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoFornecedor extends Model
{
    use HasFactory;

    protected $table = 'tb_protuto_fornecedor';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_produto',
        'id_fornecedor',
        'status',
    ];

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'id_fornecedor');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }

    public function scopeDaEmpresa($query, $idEmpresa)
    {
        return $query->join('tb_fornecedores as f', 'tb_protuto_fornecedor.id_fornecedor', '=', 'f.id_fornecedor')
            ->where('f.id_empresa', $idEmpresa)
            ->select('tb_protuto_fornecedor.*');
    }
}
