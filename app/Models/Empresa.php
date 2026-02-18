<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;
    protected $table = 'tb_empresas';
    protected $primaryKey = 'id_empresa';
    protected $fillable = [
        'nome_empresa',
        'cnpj',
        'email_empresa',
        'telefone',
        'website',
        'endereco',
        'cep',
        'cidade',
        'estado',
        'segmento',
        'status'
    ];
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_empresa');
    }
    public function filiais()
    {
        return $this->hasMany(Filial::class, 'id_empresa');
    }

    public function licencas()
    {
        return $this->hasMany(Licenca::class, 'id_empresa');
    }

    public function assinaturas()
    {
        return $this->hasMany(Assinatura::class, 'id_empresa');
    }
}
