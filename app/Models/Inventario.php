<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'tb_inventario';
    protected $primaryKey = 'id_inventario';
    protected $fillable = [
        'id_capa_inventario',
        'id_empresa',
        'id_filial',
        'id_produto',
        'quantidade_fisica',
        'quantidade_sistema',
        'motivo',
        'data_inventario',
        'id_usuario'
    ];

    protected $casts = [
        'quantidade_fisica' => 'decimal:2',
        'quantidade_sistema' => 'decimal:2',
        'data_inventario' => 'datetime'
    ];

    public function capaInventario()
    {
        return $this->belongsTo(CapaInventario::class, 'id_capa_inventario');
    }

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

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}