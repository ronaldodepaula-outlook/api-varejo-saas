<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapaInventario extends Model
{
    use HasFactory;

    protected $table = 'tb_capa_inventario';
    protected $primaryKey = 'id_capa_inventario';
    protected $fillable = [
        'id_empresa',
        'id_filial',
        'descricao',
        'data_inicio',
        'data_fechamento',
        'status',
        'observacao',
        'id_usuario'
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fechamento' => 'datetime'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'id_filial');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_capa_inventario');
    }
}