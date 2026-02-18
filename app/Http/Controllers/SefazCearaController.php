<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SefazCearaController extends Controller
{
    /**
     * Consulta uma NFC-e na SEFAZ-CE pela chave de acesso.
     */
    public function consultarPorChave($chave)
    {
        $url = "https://nfce.sefaz.ce.gov.br/pages/consultaNota.jsf";

        try {
            // Inicializa o cliente Guzzle
            $client = new Client([
                'verify' => false, // Ignora verificação SSL (autossinado)
                'timeout' => 30,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2, // Força TLS 1.2
                ],
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Connection' => 'keep-alive',
                ]
            ]);

            // Faz a requisição GET
            $response = $client->request('GET', $url, [
                'query' => [
                    'chNFe' => $chave,
                    'nVersao' => '100',
                    'tpAmb' => '1',
                ],
            ]);

            $html = (string) $response->getBody();

            // Retorna o HTML da consulta (página pública da NFC-e)
            return response()->json([
                'status' => 'sucesso',
                'mensagem' => 'Consulta realizada com sucesso.',
                'conteudo_html' => $html,
            ], 200);

        } catch (RequestException $e) {
            $mensagemErro = $e->getMessage();

            if ($e->hasResponse()) {
                $mensagemErro .= ' | HTTP Status: ' . $e->getResponse()->getStatusCode();
            }

            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Falha na consulta à SEFAZ-CE.',
                'detalhe' => $mensagemErro,
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Erro inesperado ao consultar SEFAZ-CE.',
                'detalhe' => $e->getMessage(),
            ], 500);
        }
    }
}
