<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Produto;

class Estoque extends Model
{
    use HasFactory;

    // A tabela `tb_estoque` não possui colunas created_at / updated_at.
    // Desabilitamos o gerenciamento automático de timestamps do Eloquent
    // para evitar tentativas de inserir essas colunas.
    public $timestamps = false;

    protected $table = 'tb_estoque';
    protected $primaryKey = 'id_estoque';
    protected $fillable = [
        'id_empresa',
        'id_filial',
        'id_produto',
        'quantidade',
        'estoque_minimo',
        'estoque_maximo'
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'estoque_minimo' => 'decimal:2',
        'estoque_maximo' => 'decimal:2'
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

    /**
     * Ajusta o saldo de estoque para um produto (incrementa/decrementa)
     * Retorna o novo saldo (quantidade) após o ajuste.
     * Garante operação em transação e lock para evitar condições de corrida.
     *
     * @param int $id_empresa
     * @param int|null $id_filial
     * @param int $id_produto
     * @param float $delta (positivo para entrada, negativo para saída)
     * @return float
     */
    public static function adjustStock(int $id_empresa, $id_filial, int $id_produto, $delta)
    {
        // Valida que o produto exista antes de tentar inserir/ajustar estoque.
        if (!Produto::where('id_produto', $id_produto)->exists()) {
            throw new \Exception("Produto informado não existe. id_produto={$id_produto}");
        }
        return DB::transaction(function () use ($id_empresa, $id_filial, $id_produto, $delta) {
            // Tenta bloquear a linha se existir
            $query = self::where('id_empresa', $id_empresa)
                ->where('id_produto', $id_produto);

            if ($id_filial !== null) {
                $query->where('id_filial', $id_filial);
            } else {
                $query->whereNull('id_filial');
            }

            $estoque = $query->lockForUpdate()->first();

            if (!$estoque) {
                $quantidade = $delta;
                // Evita criar com saldo negativo
                if ($quantidade < 0) $quantidade = 0;
                $estoque = self::create([
                    'id_empresa' => $id_empresa,
                    'id_filial' => $id_filial,
                    'id_produto' => $id_produto,
                    'quantidade' => $quantidade
                ]);
                return (float) $estoque->quantidade;
            }

            $novo = (float) $estoque->quantidade + (float) $delta;
            if ($novo < 0) $novo = 0;
            $estoque->quantidade = $novo;
            $estoque->save();
            return $novo;
        });
    }
}