<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContagemInventario extends Model
{
    protected $table = 'tb_contagem_inventario';
    protected $primaryKey = 'id_contagem';
    public $timestamps = true;

    protected $fillable = [
        'id_inventario',
        'id_empresa',
        'id_filial',
        'id_produto',
        'tipo_operacao',
        'quantidade',
        'observacao',
        'id_usuario',
        'data_contagem'
    ];

    public function empresa() {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function filial() {
        return $this->belongsTo(Filial::class, 'id_filial', 'id_filial');
    }

    public function produto() {
        return $this->belongsTo(Produto::class, 'id_produto', 'id_produto');
    }

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function inventario() {
        return $this->belongsTo(Inventario::class, 'id_inventario', 'id_inventario');
    }
}
