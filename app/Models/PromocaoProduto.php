<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromocaoProduto extends Model
{
    use HasFactory;

    protected $table = 'tb_promocoes_produtos';
    protected $primaryKey = 'id_promocao_produto';

    protected $fillable = [
        'id_promocao',
        'id_produto',
        'preco_normal',
        'preco_promocional',
        'quantidade_minima',
        'quantidade_pague',
        'observacao',
    ];

    protected $casts = [
        'preco_normal' => 'decimal:2',
        'preco_promocional' => 'decimal:2',
    ];

    public function promocao()
    {
        return $this->belongsTo(PromocaoCabecalho::class, 'id_promocao');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}
