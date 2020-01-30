<?php namespace NFse\Soap;

class ConsultaSituacaoLoteRps {

    private $wsResponse;
    private $error;
    private $dataLote;
    private $situacoes = [  //manual de integação pg. 24
        1 => 'Não recebido',
        2 => 'Não processado',
        3 => 'Processado com erro',
        4 => 'Processado com sucesso',
    ];

    //construtor (passar o SOAP response)
    public function __construct($wsResponse) {
        $this->wsResponse = $wsResponse;
    }

    //retorna os dados de entrada do lote após o envio
    public function getDadosLote(){
        if(is_object($this->wsResponse)){
            return $this->dataLote = [
                'numeroLote'       => $this->wsResponse->NumeroLote->__toString(),
                'situacao'         => $this->wsResponse->Situacao->__toString(),
                'descricaoSituaco' => $this->situacoes[$this->wsResponse->Situacao->__toString()],
            ];
        }else{
            $this->error = "Não foi possivel processar a resposta do servidor da prefeitura.";
            return false;
        }
    }
}
