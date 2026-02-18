<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdvCaixa extends Model
{
    use HasFactory;

    protected $table = 'tb_pdv_caixas';
    protected $primaryKey = 'id_caixa';
    public $timestamps = true;

    protected $fillable = [
        'id_empresa',
        'id_filial',
        'id_usuario',
        'data_abertura',
        'data_fechamento',
        'valor_abertura',
        'valor_fechamento',
        'status',
        'observacao'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function vendas()
    {
        return $this->hasMany(PdvVenda::class, 'id_caixa', 'id_caixa');
    }
}
