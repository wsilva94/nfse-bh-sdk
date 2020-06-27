<?php namespace Nfse\Soap;

class ErrorMsg
{

    private $webServiceResponse;
    private $messages = '';
    private $error;

    public function __construct($webServiceResponse)
    {
        $this->webServiceResponse = $webServiceResponse;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getMessages($attr = 'ListaMensagemRetorno', $infRps = false)
    {
        if (is_object($this->webServiceResponse))
        {
            $listaMensagens = $this->webServiceResponse->$attr;
            if ($this->webServiceResponse && $listaMensagens)
            {
                if (count($listaMensagens->MensagemRetorno) > 0)
                {
                    foreach ($listaMensagens->MensagemRetorno as $msg)
                    {
                        $eAdd = ($infRps) ? "RPS Nº: " . $msg->IdentificacaoRps->Numero . '. ' : '';
                        $this->messages .= "{$eAdd} " . $msg->Codigo . ' - ' . $msg->Mensagem . '<br>';
                    }
                }
                return $this->messages;

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
