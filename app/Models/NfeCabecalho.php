<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfeCabecalho extends Model
{
    protected $table = 'tb_nfe_cabecalho';
    public $timestamps = false;
    
    // Especificar a chave primária
    protected $primaryKey = 'id_nfe';

    protected $fillable = [
        'id_empresa', 'id_filial', 'cUF', 'cNF', 'natOp', 'mods', 'serie', 'nNF', 'dhEmi',
        'tpNF', 'idDest', 'cMunFG', 'tpImp', 'tpEmis', 'cDV', 'tpAmb', 'finNFe',
        'indFinal', 'indPres', 'procEmi', 'verProc', 'valor_total', 'chave_acesso'
    ];

    public function emitente() { 
        return $this->hasOne(NfeEmitente::class, 'id_nfe', 'id_nfe'); 
    }
    
    public function destinatario() { 
        return $this->hasOne(NfeDestinatario::class, 'id_nfe', 'id_nfe'); 
    }
    
    public function itens() { 
        return $this->hasMany(NfeItens::class, 'id_nfe', 'id_nfe'); 
    }
    
    public function transporte() { 
        return $this->hasOne(NfeTransporte::class, 'id_nfe', 'id_nfe'); 
    }
    
    public function cobranca() { 
        return $this->hasOne(NfeCobranca::class, 'id_nfe', 'id_nfe'); 
    }
    
    public function pagamentos() { 
        return $this->hasMany(NfePagamentos::class, 'id_nfe', 'id_nfe'); 
    }
    
    public function informacoesAdicionais() { 
        return $this->hasOne(NfeInfAdic::class, 'id_nfe', 'id_nfe'); 
    }
}