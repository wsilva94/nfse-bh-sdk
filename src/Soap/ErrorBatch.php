<?php namespace Nfse\Soap;

class ErrorBatch
{

    private $wsResponse;
    private $messages = '';
    private $error;

    //construtor (passar a resposta do SOAP call)
    public function __construct($wsResponse)
    {
        $this->wsResponse = $wsResponse;
    }

    //retorna o erro de processamento da resposta
    public function getError()
    {
        return $this->error;
    }

    //retorna as mensagens emitidas pelo webservice
    public function getProcessingErrors()
    {
        $errArray = [];
        if (is_object($this->wsResponse)) {
            $listaMensagens = $this->wsResponse->ListaMensagemRetornoLote;
            if ($this->wsResponse && $listaMensagens) {
                if (count($listaMensagens->MensagemRetorno) > 0) {
                    foreach ($listaMensagens->MensagemRetorno as $msg) {
                        $errArray[] = [
                            'numberRps' => $msg->IdentificacaoRps->Numero . "",
                            'errorRps' => $msg->Codigo . ' - ' . $msg->Mensagem,
                        ];
                    }
                }
                return ($errArray);
            } else {
                $this->error = "O servidor da prefeitura não retornou nenhuma mensagem na lista.";
                return false;
            }
        } else {
            $this->error = "Não foi possivel processar a resposta do servidor da prefeitura.";
            return false;
        }
    }
}
