<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filial extends Model
{
    protected $table = 'tb_filiais';
    protected $primaryKey = 'id_filial';
    public $timestamps = true;
    protected $fillable = [
        'id_empresa',
        'nome_filial',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'data_cadastro',
        'created_at',
        'updated_at'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
