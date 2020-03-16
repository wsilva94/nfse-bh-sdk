<?php namespace NFse\Soap;

class EnvioLoteRps
{
    private $wsResponse;
    private $error;
    private $dataLote;

    //construtor (passar o SOAP response)
    public function __construct($wsResponse)
    {
        $this->wsResponse = $wsResponse;
    }

    //retorna os dados de entrada do lote após o envio
    public function getDadosLote()
    {
        if (is_object($this->wsResponse) && isset($this->wsResponse->outputXML)) {
            $this->wsResponse      = simplexml_load_string($this->wsResponse->outputXML);
            return $this->dataLote = [
                'numeroLote'       => $this->wsResponse->NumeroLote->__toString(),
                'protocolo'        => $this->wsResponse->Protocolo->__toString(),
                'dataRecebimento'  => $this->wsResponse->DataRecebimento->__toString(),
            ];
        } else {
            $this->error = "Não foi possivel processar a resposta do servidor da prefeitura.";
            return false;
        }
    }
}
