<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    use HasFactory;

    protected $table = 'tb_fornecedores';
    protected $primaryKey = 'id_fornecedor';

    protected $fillable = [
        'id_empresa',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'inscricao_estadual',
        'contato',
        'telefone',
        'email',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'status',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function scopeDaEmpresa($query, $idEmpresa)
    {
        return $query->where('id_empresa', $idEmpresa);
    }
}
