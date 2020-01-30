<?php namespace NFse\Service;

use Exception;
use NFse\Models\Settings;

use NFse\Service\ConsultBase;
use NFse\Soap\ConsultaSituacaoLoteRps;
use NFse\Soap\ErrorMsg;
use NFse\Soap\Soap;

class BatchSituationConsultation extends ConsultBase
{
    private $xSoap;

    /**
     * constroi o xml de consulta
     *
     * @param NFse\Models\Settings;
     * @param string número de protocolo
     */
    public function __construct(Settings $settings, string $numProtocol)
    {
        $this->xSoap = new Soap($settings, 'ConsultarSituacaoLoteRpsRequest');

        $parameters = (object) [
            'numProtocol' => $numProtocol,
            'file' => 'consultaSituacaoLoteRps',
        ];

        parent::__construct();
        $this->syncModel = $this->callConsultation($settings, $parameters);

    }

    /**
     * envia a consulta para o servidor da PBH
     */
    public function sendConsultation(): object
    {
        //envia a chamada para o SOAP
        try {
            $this->xSoap->setXML($this->getXML());
            $wsResponse = $this->xSoap->__soapCall();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        //carrega o xml de resposta para um object
        $xmlResponse = simplexml_load_string($wsResponse->outputXML);
        //identifica o retorno e faz o processamento nescessário
        if (is_object($xmlResponse) && isset($xmlResponse->ListaMensagemRetorno)) {

            $wsError = new ErrorMsg($xmlResponse);
            $messages = $wsError->getMessages();

            return (object) $this->errors = ($messages) ? $messages : $wsError->getError();
        } else {
            $wsLote = new ConsultaSituacaoLoteRps($xmlResponse);
            return (object) $wsLote->getDadosLote();
        }
    }

}
