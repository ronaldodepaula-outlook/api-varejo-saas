<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assinatura extends Model
{
    protected $table = 'tb_assinaturas';
    protected $primaryKey = 'id_assinatura';
    protected $fillable = [
        'id_empresa',
        'plano',
        'valor',
        'data_inicio',
        'data_fim',
        'status'
    ];
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'id_assinatura');
    }
}
