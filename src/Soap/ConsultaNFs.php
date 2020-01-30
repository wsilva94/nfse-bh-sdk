<?php namespace NFse\Soap;

use NFse\Soap\ConsultaLoteRps;

class ConsultaNFs extends ConsultaLoteRps
{

    private $NFs;

    //construtor (passar o SOAP response)
    public function __construct($wsResponse)
    {
        return parent::__construct($wsResponse);
    }

    //retorna os dados de entrada do lote após o envio
    public function getDadosNFs()
    {
        if (is_object($this->wsResponse) && isset($this->wsResponse->outputXML)) {

            //carrega o xml da consulta
            $this->wsResponse = simplexml_load_string($this->wsResponse->outputXML);
            $nfsList = $this->wsResponse->ListaNfse->CompNfse;

            //verifica se há mais de uma nota no lote
            if (count($nfsList) > 0) {
                foreach ($nfsList as $NFS) {
                    //adiciona a nota ao array
                    $this->domDocument->loadXML($NFS->asXML());
                    $this->dataLote['nfs'][$NFS->Nfse->InfNfse->Numero->__toString()] = $this->getInfSearchNFSe($NFS->Nfse->InfNfse);
                    $this->dataLote['xml'][$NFS->Nfse->InfNfse->Numero->__toString()] = $this->domDocument->saveXML();
                }
            } else {
                $this->dataLote['nfs'] = null;
            }

            //retorna o array montado
            return $this->dataLote;

        } else {
            $this->error = "Não foi possivel processar a resposta do servidor da prefeitura.";
            return false;
        }
    }
}
