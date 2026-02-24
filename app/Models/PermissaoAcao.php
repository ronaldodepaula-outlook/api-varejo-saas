<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissaoAcao extends Model
{
    protected $table = 'tb_permissoes_acoes';
    protected $primaryKey = 'id_acao';
    public $timestamps = true;

    protected $fillable = [
        'nome_acao',
        'codigo_acao',
        'descricao',
    ];
}
