<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapaTransferencia extends Model
{
    use HasFactory;

    protected $table = 'tb_capa_transferencia';
    protected $primaryKey = 'id_capa_transferencia';
    protected $fillable = [
        'id_empresa',
        'id_filial_origem',
        'id_filial_destino',
        'data_transferencia',
        'status',
        'observacao',
        'id_usuario'
    ];

    protected $casts = [
        'data_transferencia' => 'datetime'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function filialOrigem()
    {
        return $this->belongsTo(Filial::class, 'id_filial_origem');
    }

    public function filialDestino()
    {
        return $this->belongsTo(Filial::class, 'id_filial_destino');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function transferencias()
    {
        return $this->hasMany(Transferencia::class, 'id_capa_transferencia');
    }
}