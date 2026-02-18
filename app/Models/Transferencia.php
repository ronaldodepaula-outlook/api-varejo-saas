<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    use HasFactory;

    protected $table = 'tb_transferencias';
    protected $primaryKey = 'id_transferencia';
    protected $fillable = [
        'id_empresa',
        'id_filial_origem',
        'id_filial_destino',
        'id_produto',
        'quantidade',
        'data_transferencia',
        'status',
        'observacao'
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
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

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}