<?php namespace NFse\Soap;

class CancelamentoNFs {

    private $wsResponse;
    private $error;
    private $dataCancelamento;
    private $domDocument;

    //construtor (passar o SOAP response)
    public function __construct($wsResponse) {
        $this->wsResponse  = $wsResponse;
        $this->domDocument = new \DOMDocument('1.0');
        $this->domDocument->preserveWhiteSpace = false;
        $this->domDocument->formatOutput = true;
    }

    //retorna os erros de processamento
    public function getError() {
        return $this->error;
    }

    //retorna os dados de confirmação do cancelamento
    public function getDataCancelamento(){

        if(is_object($this->wsResponse) && isset($this->wsResponse->outputXML)){

            //carrega o xml da consulta
            $this->dataCancelamento['xmlCancelamento'] = $this->wsResponse->outputXML;
            $this->wsResponse              = simplexml_load_string($this->wsResponse->outputXML);
            $retornoCancelamento           = $this->wsResponse->RetCancelamento->NfseCancelamento;

            //verifica se há mais de uma nota no lote
            if(count($retornoCancelamento) > 0){
                foreach ($retornoCancelamento as $cancelamento){
                    //adiciona a nota ao array
                    $this->domDocument->loadXML($cancelamento->asXML());
                    $this->dataCancelamento['cancelamento'][$cancelamento->Confirmacao->Pedido->InfPedidoCancelamento->IdentificacaoNfse->Numero->__toString()] = $this->getInfCancelamento($cancelamento->Confirmacao);
                    $this->dataCancelamento['xmlPedidos']  [$cancelamento->Confirmacao->Pedido->InfPedidoCancelamento->IdentificacaoNfse->Numero->__toString()] = $this->domDocument->saveXML();
                }
            }else{
                $this->dataCancelamento['cancelamento'] = null;
                $this->dataCancelamento['xmlPedidos']   = null;
            }

            //retorna o array montado
            return $this->dataCancelamento;

        }else{
            $this->error = "Não foi possivel processar a resposta do servidor da prefeitura.";
            return false;
        }
    }

    //retorna os dados da confirmação de cancelamento
    private function getInfCancelamento($confirmacao)
    {
        foreach($confirmacao->Pedido->InfPedidoCancelamento->attributes() as $a => $b) {
            $idPedido = $b->__toString();
        }

        return [
            'idPedido'       => $idPedido,
            'numeroNFs'      => $confirmacao->Pedido->InfPedidoCancelamento->IdentificacaoNfse->Numero->__toString(),
            'cnpjPrestador'  => $confirmacao->Pedido->InfPedidoCancelamento->IdentificacaoNfse->Cnpj->__toString(),
            'imPrestador'    => $confirmacao->Pedido->InfPedidoCancelamento->IdentificacaoNfse->InscricaoMunicipal->__toString(),
            'codMunicipio'   => $confirmacao->Pedido->InfPedidoCancelamento->IdentificacaoNfse->CodigoMunicipio->__toString(),
            'dataHora'       => $confirmacao->DataHora->__toString(),
        ];

    }
}

