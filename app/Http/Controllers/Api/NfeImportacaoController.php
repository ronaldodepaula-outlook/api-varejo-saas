<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class NfeImportacaoController extends Controller
{
    public function importarXML(Request $request)
    {
        try {
            $request->validate([
                'xml_file' => 'required|file|mimes:xml',
                'id_empresa' => 'required|integer',
                'id_filial' => 'nullable|integer'
            ]);

            $xmlFile = $request->file('xml_file');
            $xmlPath = $xmlFile->getRealPath();
            $xmlContent = file_get_contents($xmlPath);
            
            // Método mais robusto para lidar com namespaces
            $xmlContent = $this->cleanXmlNamespaces($xmlContent);
            
            $xml = simplexml_load_string($xmlContent);

            if (!$xml) {
                // Tenta carregar como XML simples sem tratamento de namespaces
                $xml = simplexml_load_file($xmlPath);
                if (!$xml) {
                    throw new \Exception("Arquivo XML inválido ou corrompido.");
                }
            }

            DB::beginTransaction();

            $id_empresa = $request->id_empresa;
            $id_filial = $request->id_filial ?? null;

            // Encontra a tag infNFe independentemente da estrutura
            $infNFe = $xml->NFe->infNFe ?? $xml->infNFe ?? $xml;

            if (!$infNFe) {
                throw new \Exception("Estrutura XML não reconhecida.");
            }

            // ==============================
            // VALIDAÇÃO DE DUPLICIDADE
            // ==============================
            $ide = $infNFe->ide;
            $emit = $infNFe->emit;
            $dest = $infNFe->dest;
            
            // Extrai dados para validação
            $cNF = (string) ($ide->cNF ?? '');
            $serie = (string) ($ide->serie ?? '');
            $nNF = (string) ($ide->nNF ?? '');
            $cnpj_emitente = (string) ($emit->CNPJ ?? '');
            $cnpj_destinatario = (string) ($dest->CNPJ ?? '');
            
            // Trata diferentes formatos de data
            $dhEmi = null;
            if (isset($ide->dhEmi)) {
                $dhEmi = date('Y-m-d H:i:s', strtotime((string)$ide->dhEmi));
            } elseif (isset($ide->dEmi)) {
                $dhEmi = date('Y-m-d 00:00:00', strtotime((string)$ide->dEmi));
            }

            // Verifica se a nota já existe
            $notaExistente = DB::table('tb_nfe_cabecalho as c')
                ->join('tb_nfe_emitente as e', 'c.id_nfe', '=', 'e.id_nfe')
                ->join('tb_nfe_destinatario as d', 'c.id_nfe', '=', 'd.id_nfe')
                ->where('c.cNF', $cNF)
                ->where('c.serie', $serie)
                ->where('c.nNF', $nNF)
                ->where('e.CNPJ', $cnpj_emitente)
                ->where('d.CNPJ', $cnpj_destinatario)
                ->where('c.dhEmi', $dhEmi)
                ->select('c.*', 'e.xNome as emitente_nome', 'd.xNome as destinatario_nome')
                ->first();

            if ($notaExistente) {
                DB::rollBack();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Nota fiscal já existe no sistema',
                    'nota_existente' => [
                        'id_nfe' => $notaExistente->id_nfe,
                        'numero' => $notaExistente->nNF,
                        'serie' => $notaExistente->serie,
                        'cNF' => $notaExistente->cNF,
                        'emitente' => $notaExistente->emitente_nome,
                        'destinatario' => $notaExistente->destinatario_nome,
                        'data_emissao' => $notaExistente->dhEmi,
                        'valor_total' => $notaExistente->valor_total,
                        'status' => $notaExistente->status
                    ]
                ], 409); // 409 Conflict
            }

            // ==============================
            // CABEÇALHO
            // ==============================
            $chave_acesso = '';
            if (isset($infNFe['Id'])) {
                $chave_acesso = str_replace('NFe', '', (string)$infNFe['Id']);
            }

            // Inserir cabecalho
            $id_nfe = DB::table('tb_nfe_cabecalho')->insertGetId([
                'id_empresa'   => $id_empresa,
                'id_filial'    => $id_filial,
                'cUF'          => (string) ($ide->cUF ?? ''),
                'cNF'          => $cNF,
                'natOp'        => (string) ($ide->natOp ?? ''),
                'mods'         => (string) ($ide->mod ?? ''),
                'serie'        => $serie,
                'nNF'          => $nNF,
                'dhEmi'        => $dhEmi,
                'tpNF'         => (int) ($ide->tpNF ?? 0),
                'idDest'       => (int) ($ide->idDest ?? 0),
                'cMunFG'       => (string) ($ide->cMunFG ?? ''),
                'tpImp'        => (int) ($ide->tpImp ?? 0),
                'tpEmis'       => (int) ($ide->tpEmis ?? 0),
                'cDV'          => (string) ($ide->cDV ?? ''),
                'tpAmb'        => (int) ($ide->tpAmb ?? 0),
                'finNFe'       => (int) ($ide->finNFe ?? 0),
                'indFinal'     => (int) ($ide->indFinal ?? 0),
                'indPres'      => (int) ($ide->indPres ?? 0),
                'procEmi'      => (int) ($ide->procEmi ?? 0),
                'verProc'      => (string) ($ide->verProc ?? ''),
                'chave_acesso' => $chave_acesso,
                'valor_total'  => (float) ($infNFe->total->ICMSTot->vNF ?? 0),
                'status'       => 'importada',
                'criado_em'    => now(),
                'atualizado_em' => now()
            ]);

            // ==============================
            // EMITENTE
            // ==============================
            if ($emit) {
                $endEmit = $emit->enderEmit;
                DB::table('tb_nfe_emitente')->insert([
                    'id_nfe'   => $id_nfe,
                    'CNPJ'     => $cnpj_emitente,
                    'xNome'    => (string) ($emit->xNome ?? ''),
                    'xFant'    => (string) ($emit->xFant ?? ''),
                    'IE'       => (string) ($emit->IE ?? ''),
                    'CRT'      => (string) ($emit->CRT ?? ''),
                    'xLgr'     => (string) ($endEmit->xLgr ?? ''),
                    'nro'      => (string) ($endEmit->nro ?? ''),
                    //'xCpl'     => (string) ($endEmit->xCpl ?? ''),
                    'xBairro'  => (string) ($endEmit->xBairro ?? ''),
                    'cMun'     => (string) ($endEmit->cMun ?? ''),
                    'xMun'     => (string) ($endEmit->xMun ?? ''),
                    'UF'       => (string) ($endEmit->UF ?? ''),
                    'CEP'      => (string) ($endEmit->CEP ?? ''),
                    'cPais'    => (string) ($endEmit->cPais ?? '1058'),
                    'xPais'    => (string) ($endEmit->xPais ?? 'BRASIL'),
                    'fone'     => (string) ($endEmit->fone ?? ''),
                ]);
            }

            // ==============================
            // DESTINATÁRIO
            // ==============================
            if ($dest) {
                $endDest = $dest->enderDest;
                DB::table('tb_nfe_destinatario')->insert([
                    'id_nfe'      => $id_nfe,
                    'CNPJ'        => $cnpj_destinatario,
                    'xNome'       => (string) ($dest->xNome ?? ''),
                    'indIEDest'   => (int) ($dest->indIEDest ?? 0),
                    'IE'          => (string) ($dest->IE ?? ''),
                    'email'       => (string) ($dest->email ?? ''),
                    'xLgr'        => (string) ($endDest->xLgr ?? ''),
                    'nro'         => (string) ($endDest->nro ?? ''),
                    //'xCpl'        => (string) ($endDest->xCpl ?? ''),
                    'xBairro'     => (string) ($endDest->xBairro ?? ''),
                    'cMun'        => (string) ($endDest->cMun ?? ''),
                    'xMun'        => (string) ($endDest->xMun ?? ''),
                    'UF'          => (string) ($endDest->UF ?? ''),
                    'CEP'         => (string) ($endDest->CEP ?? ''),
                    'cPais'       => (string) ($endDest->cPais ?? '1058'),
                    'xPais'       => (string) ($endDest->xPais ?? 'BRASIL'),
                    'fone'        => (string) ($endDest->fone ?? ''),
                ]);
            }

            // ==============================
            // ITENS - CORREÇÃO APLICADA AQUI
            // ==============================
            $itensCount = 0;
            if (isset($infNFe->det)) {
                // CORREÇÃO: Percorre TODOS os itens usando foreach
                foreach ($infNFe->det as $det) {
                    $prod = $det->prod;
                    $imposto = $det->imposto;
                    
                    // Tratamento do campo cEAN
                    $cEAN = (string) ($prod->cEAN ?? '');
                    $cProd = (string) ($prod->cProd ?? '');
                    
                    // Se cEAN for "SEM GTIN" ou não for um código de barras válido, usa cProd
                    if (strtoupper($cEAN) === 'SEM GTIN' || 
                        empty($cEAN) || 
                        !$this->isValidBarcode($cEAN)) {
                        $cEAN = $cProd;
                    }

                    $id_item = DB::table('tb_nfe_itens')->insertGetId([
                        'id_nfe'      => $id_nfe,
                        'nItem'       => (int) ($det['nItem'] ?? ($itensCount + 1)),
                        'cProd'       => $cProd,
                        'cEAN'        => $cEAN,
                        'xProd'       => (string) ($prod->xProd ?? ''),
                        'NCM'         => (string) ($prod->NCM ?? ''),
                        'CEST'        => (string) ($prod->CEST ?? ''),
                        'CFOP'        => (string) ($prod->CFOP ?? ''),
                        'uCom'        => (string) ($prod->uCom ?? ''),
                        'qCom'        => (float) ($prod->qCom ?? 0),
                        'vUnCom'      => (float) ($prod->vUnCom ?? 0),
                        'vProd'       => (float) ($prod->vProd ?? 0),
                        'cEANTrib'    => (string) ($prod->cEANTrib ?? $cEAN),
                        'uTrib'       => (string) ($prod->uTrib ?? ''),
                        'qTrib'       => (float) ($prod->qTrib ?? 0),
                        'vUnTrib'     => (float) ($prod->vUnTrib ?? 0),
                        'indTot'      => (int) ($prod->indTot ?? 1),
                        'xPed'        => (string) ($prod->xPed ?? ''),
                        'nItemPed'    => (string) ($prod->nItemPed ?? ''),
                        'vTotTrib'    => (float) ($prod->vTotTrib ?? 0),
                        'infAdProd'   => (string) ($prod->infAdProd ?? ''),
                    ]);

                    $itensCount++;

                    // ==============================
                    // IMPOSTOS DO ITEM
                    // ==============================
                    if ($imposto) {
                        $ICMS = $imposto->ICMS;
                        $IPI = $imposto->IPI;
                        $PIS = $imposto->PIS;
                        $COFINS = $imposto->COFINS;

                        // Encontra a primeira tag ICMS disponível
                        $icmsData = null;
                        if ($ICMS) {
                            foreach ($ICMS->children() as $icmsType) {
                                $icmsData = $icmsType;
                                break;
                            }
                        }

                        // CORREÇÃO: Tratamento para diferentes tipos de ICMS
                        $orig = '';
                        $CST = '';
                        $CSOSN = '';

                        if ($icmsData) {
                            $orig = (string) ($icmsData->orig ?? '');
                            $CST = (string) ($icmsData->CST ?? '');
                            $CSOSN = (string) ($icmsData->CSOSN ?? '');
                        }

                        // Tratamento para IPI
                        $pIPI = 0;
                        $vIPI = 0;
                        if ($IPI) {
                            if (isset($IPI->IPITrib)) {
                                $pIPI = (float) ($IPI->IPITrib->pIPI ?? 0);
                                $vIPI = (float) ($IPI->IPITrib->vIPI ?? 0);
                            } elseif (isset($IPI->IPINT)) {
                                $pIPI = (float) ($IPI->IPINT->pIPI ?? 0);
                                $vIPI = (float) ($IPI->IPINT->vIPI ?? 0);
                            }
                        }

                        // Tratamento para PIS
                        $vPIS = 0;
                        if ($PIS) {
                            if (isset($PIS->PISAliq)) {
                                $vPIS = (float) ($PIS->PISAliq->vPIS ?? 0);
                            } elseif (isset($PIS->PISNT)) {
                                $vPIS = (float) ($PIS->PISNT->vPIS ?? 0);
                            } elseif (isset($PIS->PISOutr)) {
                                $vPIS = (float) ($PIS->PISOutr->vPIS ?? 0);
                            }
                        }

                        // Tratamento para COFINS
                        $vCOFINS = 0;
                        if ($COFINS) {
                            if (isset($COFINS->COFINSAliq)) {
                                $vCOFINS = (float) ($COFINS->COFINSAliq->vCOFINS ?? 0);
                            } elseif (isset($COFINS->COFINSNT)) {
                                $vCOFINS = (float) ($COFINS->COFINSNT->vCOFINS ?? 0);
                            } elseif (isset($COFINS->COFINSOutr)) {
                                $vCOFINS = (float) ($COFINS->COFINSOutr->vCOFINS ?? 0);
                            }
                        }

                        DB::table('tb_nfe_impostos')->insert([
                            'id_item'   => $id_item,
                            'vTotTrib'  => (float) ($prod->vTotTrib ?? 0),
                            'orig'      => $orig,
                            'CSOSN'     => $CSOSN,
                            'CST'       => $CST,
                            'vBC'       => (float) ($icmsData->vBC ?? 0),
                            //'pICMS'     => (float) ($icmsData->pICMS ?? 0),
                            //'vICMS'     => (float) ($icmsData->vICMS ?? 0),
                            'pIPI'      => $pIPI,
                            'vIPI'      => $vIPI,
                            'vPIS'      => $vPIS,
                            'vCOFINS'   => $vCOFINS,
                        ]);
                    }
                }
            }

            // ==============================
            // TRANSPORTE
            // ==============================
            $transp = $infNFe->transp;
            if ($transp) {
                $transporta = $transp->transporta;
                $vol = $transp->vol;
                
                DB::table('tb_nfe_transporte')->insert([
                    'id_nfe'     => $id_nfe,
                    'modFrete'   => (int) ($transp->modFrete ?? 0),
                    'CNPJ'       => (string) ($transporta->CNPJ ?? ''),
                    'xNome'      => (string) ($transporta->xNome ?? ''),
                    'IE'         => (string) ($transporta->IE ?? ''),
                    'xEnder'     => (string) ($transporta->xEnder ?? ''),
                    'xMun'       => (string) ($transporta->xMun ?? ''),
                    'UF'         => (string) ($transporta->UF ?? ''),
                    'esp'        => (string) ($vol->esp ?? ''),
                    'pesoL'      => (float) ($vol->pesoL ?? 0),
                    'pesoB'      => (float) ($vol->pesoB ?? 0),
                ]);
            }

            // ==============================
            // COBRANÇA
            // ==============================
            $cobr = $infNFe->cobr;
            if ($cobr) {
                $fat = $cobr->fat;
                $id_cobranca = DB::table('tb_nfe_cobranca')->insertGetId([
                    'id_nfe' => $id_nfe,
                    'nFat'   => (string) ($fat->nFat ?? ''),
                    'vOrig'  => (float) ($fat->vOrig ?? 0),
                    'vDesc'  => (float) ($fat->vDesc ?? 0),
                    'vLiq'   => (float) ($fat->vLiq ?? 0),
                ]);

                // ==============================
                // DUPLICATAS
                // ==============================
                if (isset($cobr->dup)) {
                    // CORREÇÃO: Percorre todas as duplicatas
                    foreach ($cobr->dup as $dup) {
                        $dVenc = null;
                        if (!empty($dup->dVenc)) {
                            $dVenc = date('Y-m-d', strtotime((string)$dup->dVenc));
                        }
                        
                        DB::table('tb_nfe_duplicatas')->insert([
                            'id_cobranca' => $id_cobranca,
                            'nDup'        => (string) ($dup->nDup ?? ''),
                            'dVenc'       => $dVenc,
                            'vDup'        => (float) ($dup->vDup ?? 0),
                        ]);
                    }
                }
            }

            // ==============================
            // PAGAMENTOS
            // ==============================
            $pag = $infNFe->pag;
            if ($pag) {
                // CORREÇÃO: Percorre todos os pagamentos
                if (isset($pag->detPag)) {
                    foreach ($pag->detPag as $detPag) {
                        DB::table('tb_nfe_pagamentos')->insert([
                            'id_nfe' => $id_nfe,
                            'indPag' => (int) ($detPag->indPag ?? 0),
                            'tPag'   => (string) ($detPag->tPag ?? ''),
                            'vPag'   => (float) ($detPag->vPag ?? 0),
                        ]);
                    }
                }
            }

            // ==============================
            // INFORMAÇÕES ADICIONAIS
            // ==============================
            $infAdic = $infNFe->infAdic;
            if ($infAdic) {
                DB::table('tb_nfe_informacoes_adicionais')->insert([
                    'id_nfe'  => $id_nfe,
                    'infCpl'  => (string) ($infAdic->infCpl ?? ''),
                ]);
            }

            DB::commit();

            // Recupera os dados completos para retorno
            $dados = DB::table('tb_nfe_cabecalho as c')
                ->leftJoin('tb_nfe_emitente as e', 'c.id_nfe', '=', 'e.id_nfe')
                ->leftJoin('tb_nfe_destinatario as d', 'c.id_nfe', '=', 'd.id_nfe')
                ->where('c.id_nfe', $id_nfe)
                ->select('c.*', 'e.xNome as emitente_nome', 'd.xNome as destinatario_nome')
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'XML importado com sucesso!',
                'id_nfe' => $id_nfe,
                'nota_fiscal' => [
                    'numero' => $dados->nNF ?? '',
                    'serie' => $dados->serie ?? '',
                    'chave_acesso' => $dados->chave_acesso ?? '',
                    'valor_total' => $dados->valor_total ?? 0,
                    'itens_importados' => $itensCount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao importar XML NFe', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao importar XML',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica se é um código de barras válido
     */
    private function isValidBarcode($code)
    {
        if (empty($code)) {
            return false;
        }

        // Remove espaços e caracteres especiais
        $code = preg_replace('/[^0-9]/', '', $code);
        
        // Verifica se tem comprimento adequado para códigos de barras comuns
        // EAN-13: 13 dígitos, EAN-8: 8 dígitos, UPC-A: 12 dígitos
        $length = strlen($code);
        if (!in_array($length, [8, 12, 13, 14])) {
            return false;
        }

        // Verifica se é numérico
        if (!is_numeric($code)) {
            return false;
        }

        // Verifica se não é uma sequência de zeros
        if (intval($code) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Limpa namespaces do XML de forma mais robusta
     */
    private function cleanXmlNamespaces($xmlContent)
    {
        // Remove declarações de namespace problemáticas
        $xmlContent = preg_replace('/<nfeProc[^>]*>/', '<nfeProc>', $xmlContent);
        $xmlContent = preg_replace('/<NFe[^>]*>/', '<NFe>', $xmlContent);
        $xmlContent = preg_replace('/<infNFe[^>]*>/', '<infNFe>', $xmlContent);
        
        // Remove atributos de schema location
        $xmlContent = preg_replace('/xsi:schemaLocation="[^"]*"/', '', $xmlContent);
        $xmlContent = preg_replace('/xmlns:xsi="[^"]*"/', '', $xmlContent);
        $xmlContent = preg_replace('/xmlns:ds="[^"]*"/', '', $xmlContent);
        $xmlContent = preg_replace('/xmlns="[^"]*"/', '', $xmlContent);
        
        // Remove prefixos de namespace
        $xmlContent = str_replace(['xsi:', 'ds:'], '', $xmlContent);
        
        return $xmlContent;
    }

    /**
     * Lista todas as NFe importadas
     */
    public function index(Request $request)
    {
        try {
            $query = DB::table('tb_nfe_cabecalho as c')
                ->leftJoin('tb_nfe_emitente as e', 'c.id_nfe', '=', 'e.id_nfe')
                ->leftJoin('tb_nfe_destinatario as d', 'c.id_nfe', '=', 'd.id_nfe')
                ->select('c.*', 'e.xNome as emitente_nome', 'd.xNome as destinatario_nome');

            if ($request->has('id_empresa')) {
                $query->where('c.id_empresa', $request->id_empresa);
            }

            if ($request->has('status')) {
                $query->where('c.status', $request->status);
            }

            $nfe = $query->orderBy('c.criado_em', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $nfe->items(),
                'total' => $nfe->total(),
                'current_page' => $nfe->currentPage()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar NFe',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibe uma NFe específica
     */
    public function show($id)
    {
        try {
            // Busca cabecalho
            $cabecalho = DB::table('tb_nfe_cabecalho')->where('id_nfe', $id)->first();
            
            if (!$cabecalho) {
                return response()->json([
                    'success' => false,
                    'message' => 'NFe não encontrada'
                ], 404);
            }

            // Busca dados relacionados
            $emitente = DB::table('tb_nfe_emitente')->where('id_nfe', $id)->first();
            $destinatario = DB::table('tb_nfe_destinatario')->where('id_nfe', $id)->first();
            $itens = DB::table('tb_nfe_itens')->where('id_nfe', $id)->get();
            $transporte = DB::table('tb_nfe_transporte')->where('id_nfe', $id)->first();
            $cobranca = DB::table('tb_nfe_cobranca')->where('id_nfe', $id)->first();
            $pagamentos = DB::table('tb_nfe_pagamentos')->where('id_nfe', $id)->get();
            $informacoes = DB::table('tb_nfe_informacoes_adicionais')->where('id_nfe', $id)->first();

            // Busca duplicatas se existir cobrança
            $duplicatas = [];
            if ($cobranca) {
                $duplicatas = DB::table('tb_nfe_duplicatas')->where('id_cobranca', $cobranca->id_cobranca)->get();
            }

            // Busca impostos para cada item
            $itensComImpostos = [];
            foreach ($itens as $item) {
                $impostos = DB::table('tb_nfe_impostos')->where('id_item', $item->id_item)->first();
                $item->impostos = $impostos;
                $itensComImpostos[] = $item;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'cabecalho' => $cabecalho,
                    'emitente' => $emitente,
                    'destinatario' => $destinatario,
                    'itens' => $itensComImpostos,
                    'transporte' => $transporte,
                    'cobranca' => $cobranca,
                    'duplicatas' => $duplicatas,
                    'pagamentos' => $pagamentos,
                    'informacoes_adicionais' => $informacoes
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar NFe',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove uma NFe
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Verifica se a NFe existe
            $nfe = DB::table('tb_nfe_cabecalho')->where('id_nfe', $id)->first();
            if (!$nfe) {
                return response()->json([
                    'success' => false,
                    'message' => 'NFe não encontrada'
                ], 404);
            }

            // Remove registros relacionados
            DB::table('tb_nfe_emitente')->where('id_nfe', $id)->delete();
            DB::table('tb_nfe_destinatario')->where('id_nfe', $id)->delete();
            
            // Remove itens e seus impostos
            $itens = DB::table('tb_nfe_itens')->where('id_nfe', $id)->get();
            foreach ($itens as $item) {
                DB::table('tb_nfe_impostos')->where('id_item', $item->id_item)->delete();
            }
            DB::table('tb_nfe_itens')->where('id_nfe', $id)->delete();
            
            // Remove outros relacionamentos
            DB::table('tb_nfe_transporte')->where('id_nfe', $id)->delete();
            
            // Remove cobrança e duplicatas
            $cobrancas = DB::table('tb_nfe_cobranca')->where('id_nfe', $id)->get();
            foreach ($cobrancas as $cobranca) {
                DB::table('tb_nfe_duplicatas')->where('id_cobranca', $cobranca->id_cobranca)->delete();
            }
            DB::table('tb_nfe_cobranca')->where('id_nfe', $id)->delete();
            
            DB::table('tb_nfe_pagamentos')->where('id_nfe', $id)->delete();
            DB::table('tb_nfe_informacoes_adicionais')->where('id_nfe', $id)->delete();

            // Remove a NFe principal
            DB::table('tb_nfe_cabecalho')->where('id_nfe', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'NFe removida com sucesso'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover NFe',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}