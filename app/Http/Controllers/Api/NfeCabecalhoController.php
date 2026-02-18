<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NfeCabecalho;

class NfeCabecalhoController extends Controller
{
    public function importarXml(Request $request)
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml',
            'id_empresa' => 'required|integer',
            'id_filial' => 'nullable|integer',
        ]);

        $xmlPath = $request->file('xml_file')->getRealPath();
        $id_empresa = $request->id_empresa;
        $id_filial = $request->id_filial;

        try {
            $xml = simplexml_load_file($xmlPath);

            DB::beginTransaction();

            // -----------------------------
            // CABEÇALHO
            // -----------------------------
            $cabecalho = NfeCabecalho::create([
                'id_empresa' => $id_empresa,
                'id_filial' => $id_filial,
                'cUF' => (string)($xml->ide->cUF ?? null),
                'cNF' => (string)($xml->ide->cNF ?? null),
                'natOp' => (string)($xml->ide->natOp ?? null),
                'mods' => (string)($xml->ide->mod ?? null),
                'serie' => (string)($xml->ide->serie ?? null),
                'nNF' => (string)($xml->ide->nNF ?? null),
                'dhEmi' => isset($xml->ide->dhEmi) ? date('Y-m-d H:i:s', strtotime($xml->ide->dhEmi)) : null,
                'tpNF' => (int)($xml->ide->tpNF ?? null),
                'idDest' => (int)($xml->ide->idDest ?? null),
                'cMunFG' => (string)($xml->ide->cMunFG ?? null),
                'tpImp' => (int)($xml->ide->tpImp ?? null),
                'tpEmis' => (int)($xml->ide->tpEmis ?? null),
                'cDV' => (string)($xml->ide->cDV ?? null),
                'tpAmb' => (int)($xml->ide->tpAmb ?? null),
                'finNFe' => (int)($xml->ide->finNFe ?? null),
                'indFinal' => (int)($xml->ide->indFinal ?? null),
                'indPres' => (int)($xml->ide->indPres ?? null),
                'procEmi' => (int)($xml->ide->procEmi ?? null),
                'verProc' => (string)($xml->ide->verProc ?? null),
                'valor_total' => isset($xml->total->ICMSTot->vNF) ? (float)$xml->total->ICMSTot->vNF : 0,
                'chave_acesso' => (string)($xml['Id'] ?? null),
            ]);

            // -----------------------------
            // EMITENTE
            // -----------------------------
            if (isset($xml->emit)) {
                $cabecalho->emitente()->create([
                    'CNPJ' => (string)($xml->emit->CNPJ ?? null),
                    'xNome' => (string)($xml->emit->xNome ?? null),
                    'xFant' => (string)($xml->emit->xFant ?? null),
                    'xLgr' => (string)($xml->emit->enderEmit->xLgr ?? null),
                    'nro' => (string)($xml->emit->enderEmit->nro ?? null),
                    'xCpl' => (string)($xml->emit->enderEmit->xCpl ?? null),
                    'xBairro' => (string)($xml->emit->enderEmit->xBairro ?? null),
                    'cMun' => (string)($xml->emit->enderEmit->cMun ?? null),
                    'xMun' => (string)($xml->emit->enderEmit->xMun ?? null),
                    'UF' => (string)($xml->emit->enderEmit->UF ?? null),
                    'CEP' => (string)($xml->emit->enderEmit->CEP ?? null),
                    'cPais' => (string)($xml->emit->enderEmit->cPais ?? null),
                    'xPais' => (string)($xml->emit->enderEmit->xPais ?? null),
                    'fone' => (string)($xml->emit->enderEmit->fone ?? null),
                    'IE' => (string)($xml->emit->IE ?? null),
                    'CRT' => (string)($xml->emit->CRT ?? null),
                ]);
            }

            // -----------------------------
            // DESTINATÁRIO
            // -----------------------------
            if (isset($xml->dest)) {
                $cabecalho->destinatario()->create([
                    'CNPJ' => (string)($xml->dest->CNPJ ?? null),
                    'xNome' => (string)($xml->dest->xNome ?? null),
                    'xLgr' => (string)($xml->dest->enderDest->xLgr ?? null),
                    'nro' => (string)($xml->dest->enderDest->nro ?? null),
                    'xBairro' => (string)($xml->dest->enderDest->xBairro ?? null),
                    'cMun' => (string)($xml->dest->enderDest->cMun ?? null),
                    'xMun' => (string)($xml->dest->enderDest->xMun ?? null),
                    'UF' => (string)($xml->dest->enderDest->UF ?? null),
                    'CEP' => (string)($xml->dest->enderDest->CEP ?? null),
                    'cPais' => (string)($xml->dest->enderDest->cPais ?? null),
                    'xPais' => (string)($xml->dest->enderDest->xPais ?? null),
                    'fone' => (string)($xml->dest->enderDest->fone ?? null),
                    'indIEDest' => (int)($xml->dest->indIEDest ?? null),
                    'IE' => (string)($xml->dest->IE ?? null),
                    'email' => (string)($xml->dest->email ?? null),
                ]);
            }

            // -----------------------------
            // ITENS
            // -----------------------------
            foreach ($xml->det as $det) {
                $item = $cabecalho->itens()->create([
                    'id_produto' => null, // não valida produto
                    'nItem' => (int)($det['nItem'] ?? null),
                    'cProd' => (string)($det->prod->cProd ?? null),
                    'cEAN' => (string)($det->prod->cEAN ?? null),
                    'xProd' => (string)($det->prod->xProd ?? null),
                    'NCM' => (string)($det->prod->NCM ?? null),
                    'CEST' => (string)($det->prod->CEST ?? null),
                    'CFOP' => (string)($det->prod->CFOP ?? null),
                    'uCom' => (string)($det->prod->uCom ?? null),
                    'qCom' => (float)($det->prod->qCom ?? 0),
                    'vUnCom' => (float)($det->prod->vUnCom ?? 0),
                    'vProd' => (float)($det->prod->vProd ?? 0),
                    'cEANTrib' => (string)($det->prod->cEANTrib ?? null),
                    'uTrib' => (string)($det->prod->uTrib ?? null),
                    'qTrib' => (float)($det->prod->qTrib ?? 0),
                    'vUnTrib' => (float)($det->prod->vUnTrib ?? 0),
                    'indTot' => (int)($det->prod->indTot ?? null),
                    'xPed' => (string)($det->prod->xPed ?? null),
                    'nItemPed' => (string)($det->prod->nItemPed ?? null),
                    'infAdProd' => (string)($det->infAdProd ?? null),
                ]);

                // -----------------------------
                // IMPOSTOS
                // -----------------------------
                if (isset($det->imposto)) {
                    $item->impostos()->create([
                        'vTotTrib' => (float)($det->imposto->vTotTrib ?? 0),
                        'orig' => (string)($det->imposto->orig ?? null),
                        'CSOSN' => (string)($det->imposto->CSOSN ?? null),
                        'cEnq' => (string)($det->imposto->cEnq ?? null),
                        'CST' => (string)($det->imposto->CST ?? null),
                        'vBC' => (float)($det->imposto->vBC ?? 0),
                        'pIPI' => (float)($det->imposto->pIPI ?? 0),
                        'vIPI' => (float)($det->imposto->vIPI ?? 0),
                        'vPIS' => (float)($det->imposto->vPIS ?? 0),
                        'vCOFINS' => (float)($det->imposto->vCOFINS ?? 0),
                    ]);
                }
            }

            // -----------------------------
            // TRANSPORTE
            // -----------------------------
            if (isset($xml->transp)) {
                $cabecalho->transporte()->create([
                    'modFrete' => (int)($xml->transp->modFrete ?? null),
                    'CNPJ' => (string)($xml->transp->transporta->CNPJ ?? null),
                    'xNome' => (string)($xml->transp->transporta->xNome ?? null),
                    'IE' => (string)($xml->transp->transporta->IE ?? null),
                    'xEnder' => (string)($xml->transp->transporta->xEnder ?? null),
                    'xMun' => (string)($xml->transp->transporta->xMun ?? null),
                    'UF' => (string)($xml->transp->transporta->UF ?? null),
                    'esp' => (string)($xml->transp->transporta->esp ?? null),
                    'pesoL' => (float)($xml->transp->pesoL ?? 0),
                    'pesoB' => (float)($xml->transp->pesoB ?? 0),
                ]);
            }

            // -----------------------------
            // COBRANÇA E DUPLICATAS
            // -----------------------------
            if (isset($xml->cobr->fat)) {
                $fat = $xml->cobr->fat;
                $cobranca = $cabecalho->cobranca()->create([
                    'nFat' => (string)($fat->nFat ?? null),
                    'vOrig' => (float)($fat->vOrig ?? 0),
                    'vDesc' => (float)($fat->vDesc ?? 0),
                    'vLiq' => (float)($fat->vLiq ?? 0),
                ]);

                if (isset($xml->cobr->dup)) {
                    foreach ($xml->cobr->dup as $dup) {
                        $cobranca->duplicatas()->create([
                            'nDup' => (string)($dup->nDup ?? null),
                            'dVenc' => isset($dup->dVenc) ? date('Y-m-d', strtotime($dup->dVenc)) : null,
                            'vDup' => (float)($dup->vDup ?? 0),
                        ]);
                    }
                }
            }

            // -----------------------------
            // PAGAMENTOS
            // -----------------------------
            if (isset($xml->pag->detPag)) {
                foreach ($xml->pag->detPag as $pag) {
                    $cabecalho->pagamentos()->create([
                        'indPag' => (int)($pag->indPag ?? null),
                        'tPag' => (string)($pag->tPag ?? null),
                        'vPag' => (float)($pag->vPag ?? 0),
                    ]);
                }
            }

            // -----------------------------
            // INFORMAÇÕES ADICIONAIS
            // -----------------------------
            if (isset($xml->infAdic)) {
                $cabecalho->informacoesAdicionais()->create([
                    'infCpl' => (string)($xml->infAdic->infCpl ?? null),
                ]);
            }

            DB::commit();

            // Carrega relacionamentos
            $cabecalho->load([
                'emitente',
                'destinatario',
                'itens.impostos',
                'transporte',
                'cobranca.duplicatas',
                'pagamentos',
                'informacoesAdicionais'
            ]);

            return response()->json($cabecalho);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao importar XML',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
