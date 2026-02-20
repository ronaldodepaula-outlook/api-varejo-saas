<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrecoHistorico extends Model
{
    use HasFactory;

    protected $table = 'tb_precos_historico';
    protected $primaryKey = 'id_historico';
    public $timestamps = false;

    protected $fillable = [
        'id_empresa',
        'id_produto',
        'tipo_alteracao',
        'preco_anterior',
        'preco_novo',
        'motivo',
        'id_fornecedor',
        'id_usuario',
        'ip_origem',
        'data_alteracao',
    ];

    protected $casts = [
        'preco_anterior' => 'decimal:2',
        'preco_novo' => 'decimal:2',
        'data_alteracao' => 'datetime',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'id_fornecedor');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
