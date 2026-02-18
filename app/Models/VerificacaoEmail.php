<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificacaoEmail extends Model
{
    use HasFactory;

    protected $table = 'tb_verificacoes_email';
    protected $primaryKey = 'id_verificacao';

    protected $fillable = [
        'id_usuario',
        'token'
    ];
}
