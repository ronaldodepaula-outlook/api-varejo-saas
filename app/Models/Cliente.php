<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model {
    protected $table = 'tb_clientes';
    protected $primaryKey = 'id_cliente';
    public $timestamps = false;
    
    protected $fillable = [
        'id_empresa', 'id_usuario', 'nome_cliente', 'email', 'telefone', 'whatsapp', 
        'endereco', 'cidade', 'estado', 'cep', 'cpf_hash', 'cpf_encrypted',
        'document_type', 'document_number_encrypted', 'consent_marketing',
        'id_consentimento', 'classificacao', 'status', 'data_reter_ate',
        'flag_excluido_logico', 'data_exclusao_solicitada'
    ];

    protected $attributes = [
        'consent_marketing' => 'nao',
        'classificacao' => 'bronze',
        'status' => 'ativo',
        'flag_excluido_logico' => 0
    ];

    // Define os tipos de enums disponíveis
    const DOCUMENT_TYPES = ['cpf', 'cnpj', 'rg', 'outro'];
    const CONSENT_OPTIONS = ['sim', 'nao'];
    const CLASSIFICACOES = ['bronze', 'prata', 'ouro', 'diamante'];
    const STATUS_OPTIONS = ['ativo', 'inativo'];

    public function vendas() { return $this->hasMany(VendaAssistida::class,'id_cliente','id_cliente'); }
    public function debitos() { return $this->hasMany(DebitoCliente::class,'id_cliente','id_cliente'); }
}
