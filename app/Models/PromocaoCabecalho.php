<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromocaoCabecalho extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tb_promocoes_cabecalho';
    protected $primaryKey = 'id_promocao';

    protected $fillable = [
        'id_empresa',
        'id_filial',
        'codigo_promocao',
        'nome_promocao',
        'descricao',
        'tipo_promocao',
        'data_inicio',
        'data_fim',
        'status',
        'prioridade',
        'aplicar_em_todas_filiais',
        'observacoes',
        'id_usuario_criador',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'aplicar_em_todas_filiais' => 'boolean',
    ];

    public function produtos()
    {
        return $this->hasMany(PromocaoProduto::class, 'id_promocao');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'id_filial');
    }

    public function usuarioCriador()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_criador');
    }
}
