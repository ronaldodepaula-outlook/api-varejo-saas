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
            // CABEÇALHO
            // ==============================
            $ide = $infNFe->ide;
            $dhEmi = null;
            
            // Trata diferentes formatos de data
            if (isset($ide->dhEmi)) {
                $dhEmi = date('Y-m-d H:i:s', strtotime((string)$ide->dhEmi));
            } elseif (isset($ide->dEmi)) {
                $dhEmi = date('Y-m-d 00:00:00', strtotime((string)$ide->dEmi));
            }

            $chave_acesso = '';
            if (isset($infNFe['Id'])) {
                $chave_acesso = str_replace('NFe', '', (string)$infNFe['Id']);
            }

            // Inserir cabecalho
            $id_nfe = DB::table('tb_nfe_cabecalho')->insertGetId([
                'id_empresa'   => $id_empresa,
                'id_filial'    => $id_filial,
                'cUF'          => (string) ($ide->cUF ?? ''),
                'cNF'          => (string) ($ide->cNF ?? ''),
                'natOp'        => (string) ($ide->natOp ?? ''),
                'mods'         => (string) ($ide->mod ?? ''),
                'serie'        => (string) ($ide->serie ?? ''),
                'nNF'          => (string) ($ide->nNF ?? ''),
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
            $emit = $infNFe->emit;
            if ($emit) {
                $endEmit = $emit->enderEmit;
                DB::table('tb_nfe_emitente')->insert([
                    'id_nfe'   => $id_nfe,
                    'CNPJ'     => (string) ($emit->CNPJ ?? ''),
                    'xNome'    => (string) ($emit->xNome ?? ''),
                    'xFant'    => (string) ($emit->xFant ?? ''),
                    'IE'       => (string) ($emit->IE ?? ''),
                    'CRT'      => (string) ($emit->CRT ?? ''),
                    'xLgr'     => (string) ($endEmit->xLgr ?? ''),
                    'nro'      => (string) ($endEmit->nro ?? ''),
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
            $dest = $infNFe->dest;
            if ($dest) {
                $endDest = $dest->enderDest;
                DB::table('tb_nfe_destinatario')->insert([
                    'id_nfe'      => $id_nfe,
                    'CNPJ'        => (string) ($dest->CNPJ ?? ''),
                    'xNome'       => (string) ($dest->xNome ?? ''),
                    'indIEDest'   => (int) ($dest->indIEDest ?? 0),
                    'IE'          => (string) ($dest->IE ?? ''),
                    'email'       => (string) ($dest->email ?? ''),
                    'xLgr'        => (string) ($endDest->xLgr ?? ''),
                    'nro'         => (string) ($endDest->nro ?? ''),
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
            // ITENS
            // ==============================
            $itensCount = 0;
            if (isset($infNFe->det)) {
                // Converte para array se for apenas um item
                $dets = is_array($infNFe->det) ? $infNFe->det : [$infNFe->det];
                
                foreach ($dets as $det) {
                    $prod = $det->prod;
                    $imposto = $det->imposto;
                    
                    $id_item = DB::table('tb_nfe_itens')->insertGetId([
                        'id_nfe'      => $id_nfe,
                        'nItem'       => (int) ($det['nItem'] ?? ($itensCount + 1)),
                        'cProd'       => (string) ($prod->cProd ?? ''),
                        'cEAN'        => (string) ($prod->cEAN ?? ''),
                        'xProd'       => (string) ($prod->xProd ?? ''),
                        'NCM'         => (string) ($prod->NCM ?? ''),
                        'CEST'        => (string) ($prod->CEST ?? ''),
                        'CFOP'        => (string) ($prod->CFOP ?? ''),
                        'uCom'        => (string) ($prod->uCom ?? ''),
                        'qCom'        => (float) ($prod->qCom ?? 0),
                        'vUnCom'      => (float) ($prod->vUnCom ?? 0),
                        'vProd'       => (float) ($prod->vProd ?? 0),
                        'cEANTrib'    => (string) ($prod->cEANTrib ?? ''),
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

                        DB::table('tb_nfe_impostos')->insert([
                            'id_item'   => $id_item,
                            'vTotTrib'  => (float) ($prod->vTotTrib ?? 0),
                            'orig'      => (string) ($icmsData->orig ?? ''),
                            'CSOSN'     => (string) ($icmsData->CSOSN ?? ''),
                            'CST'       => (string) ($icmsData->CST ?? ''),
                            'vBC'       => (float) ($icmsData->vBC ?? 0),
                            'pIPI'      => (float) ($IPI->IPITrib->pIPI ?? ($IPI->IPINT->pIPI ?? 0)),
                            'vIPI'      => (float) ($IPI->IPITrib->vIPI ?? ($IPI->IPINT->vIPI ?? 0)),
                            'vPIS'      => (float) ($PIS->PISAliq->vPIS ?? ($PIS->PISNT->vPIS ?? 0)),
                            'vCOFINS'   => (float) ($COFINS->COFINSAliq->vCOFINS ?? ($COFINS->COFINSNT->vCOFINS ?? 0)),
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
                    $dups = is_array($cobr->dup) ? $cobr->dup : [$cobr->dup];
                    foreach ($dups as $dup) {
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
                // Verifica se é array ou objeto único
                $detPagList = [];
                if (isset($pag->detPag)) {
                    $detPagList = is_array($pag->detPag) ? $pag->detPag : [$pag->detPag];
                }

                foreach ($detPagList as $detPag) {
                    if ($detPag) {
                        DB::table('tb_nfe_pagamentos')->insert([
                            'id_nfe' => $id_nfe,
                            'indPag' => (int) ($pag->indPag ?? 0),
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
     * Método alternativo para parsing de XML com namespaces
     */
    private function parseXmlWithNamespaces($xmlPath)
    {
        try {
            // Carrega o XML mantendo os namespaces
            $xml = simplexml_load_file($xmlPath);
            if (!$xml) {
                return null;
            }

            // Registra os namespaces
            $namespaces = $xml->getNamespaces(true);
            
            // Tenta encontrar a infNFe em diferentes namespaces
            $infNFe = null;
            foreach ($namespaces as $ns) {
                if ($xml->NFe) {
                    $nfeWithNs = $xml->children($ns)->NFe;
                    if ($nfeWithNs && $nfeWithNs->infNFe) {
                        $infNFe = $nfeWithNs->infNFe;
                        break;
                    }
                }
                
                if ($xml->infNFe) {
                    $infNFe = $xml->children($ns)->infNFe;
                    if ($infNFe) break;
                }
            }

            // Se não encontrou com namespaces, tenta sem namespaces
            if (!$infNFe) {
                $infNFe = $xml->NFe->infNFe ?? $xml->infNFe ?? $xml;
            }

            return $infNFe;

        } catch (\Exception $e) {
            return null;
        }
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