<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrecoVigente extends Model
{
    use HasFactory;

    protected $table = 'tb_preco_vigente';
    protected $primaryKey = 'id_preco_vigente';
    public $timestamps = false;

    protected $fillable = [
        'id_empresa',
        'id_filial',
        'id_produto',
        'id_promocao_ativa',
        'preco_base',
        'preco_atual',
        'preco_promocional',
        'em_promocao',
        'data_inicio_promocao',
        'data_fim_promocao',
    ];

    protected $casts = [
        'preco_base' => 'decimal:2',
        'preco_atual' => 'decimal:2',
        'preco_promocional' => 'decimal:2',
        'em_promocao' => 'boolean',
        'data_inicio_promocao' => 'datetime',
        'data_fim_promocao' => 'datetime',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'id_filial');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }

    public function promocaoAtiva()
    {
        return $this->belongsTo(PromocaoCabecalho::class, 'id_promocao_ativa');
    }
}
