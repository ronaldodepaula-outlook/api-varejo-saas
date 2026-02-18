<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimentacao extends Model
{
    use HasFactory;

    // A tabela tb_movimentacoes usa `data_movimentacao` em vez de created_at/updated_at
    public $timestamps = false;

    protected $table = 'tb_movimentacoes';
    protected $primaryKey = 'id_movimentacao';
    protected $fillable = [
        'id_empresa',
        'id_filial',
        'id_produto',
        'tipo_movimentacao',
        'origem',
        'id_referencia',
        'quantidade',
        'saldo_anterior',
        'saldo_atual',
        'custo_unitario',
        'observacao',
        'id_usuario',
        'data_movimentacao'
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'saldo_anterior' => 'decimal:2',
        'saldo_atual' => 'decimal:2',
        'custo_unitario' => 'decimal:2',
        'data_movimentacao' => 'datetime'
    ];

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

    /**
     * Normaliza valores de origem que não existem no enum do banco
     * Quando não for possível alterar o schema, mapeamos origens custom
     * para um valor já suportado (por exemplo 'manual').
     *
     * @param string $origem
     * @return string
     */
    public static function normalizeOrigem(?string $origem)
    {
        if (empty($origem)) return 'manual';
        $map = [
            'venda_assistida' => 'manual',
            'cancelamento_venda_assistida' => 'manual',
            'cancelamento_item' => 'manual'
        ];
        return $map[$origem] ?? $origem;
    }
}