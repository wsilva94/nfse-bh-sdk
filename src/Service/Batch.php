<?php namespace Nfse\Service;

use Exception;
use Nfse\Provider\Settings;
use NFse\Sanitizers\Text;
use Nfse\Service\Base;
use Nfse\Soap\Soap;
use Nfse\Soap\ErrorMsg;
use Nfse\Soap\BatchRPS;
use Nfse\Soap\ErrorBatch;

class Batch extends Base
{
    private $xSoap;

    public function __construct(Settings $settings, string $protocol)
    {
        parent::__construct();
        $this->text = new Text();
        $this->xSoap = new Soap($settings, 'ConsultarLoteRpsRequest');
        $this->syncModel = $this->loadConsultXML($settings, ['numProtocol' => $protocol, 'file' => 'consultRPSBatch']);
    }

    public function sendConsultation()
    {
        $webServiceResponse = $this->sendXMLWebService();
        $xmlResponse = $this->loadXMLToString($webServiceResponse);
        $this->normalizeReponseWebService($xmlResponse , $webServiceResponse);
    }

    private function sendXMLWebService()
    {
        try {
            $this->xSoap->setXML($this->getXML());
            return $this->xSoap->__soapCall();
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    private function loadXMLToString(object $webServiceResponse)
    {
        return simplexml_load_string($webServiceResponse->outputXML);
    }

    private function normalizeReponseWebService(object $xmlResponse , $webServiceResponse)
    {
        if (is_object($xmlResponse) && isset($xmlResponse->ListaMensagemRetornoLote)) {
            $wsError = new ErrorMsg($xmlResponse);
            $messages = $wsError->getMessages('ListaMensagemRetornoLote', true);

            return $this->errors = ($messages) ? $messages : $wsError->getError();
        } else {
            $wsLote = new BatchRPS($webServiceResponse);
            return $wsLote->getDadosLote();
        }
    }

    //retorna a lista de erros de processamento do lote
    public function getProcessingErrors()
    {
        try { //envia a chamada para o SOAP
            $this->xSoap->setXML($this->syncModel->getXML());
            $wsResponse = $this->xSoap->__soapCall();
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }

        $xmlResponse = simplexml_load_string($wsResponse->outputXML);
        if (is_object($xmlResponse) && isset($xmlResponse->ListaMensagemRetornoLote)) {
            $wsErrorLote = new ErrorBatch($xmlResponse);
            $parseErrors = $wsErrorLote->getProcessingErrors();
            if (is_array($parseErrors)) {
                return $parseErrors;
            } else {
                $this->errors = $wsErrorLote->getError();
                return false;
            }
        } else {

            if (isset($xmlResponse->ListaNfse)) {
                $this->errors = "O serviço da prefeitura não retornou nenhum erro de processamento neste lote.";
            } else {
                $this->errors = 'Nenhuma informação sobre este lote foi encontrado no serviço da prefeitura.';
            }

            return false;
        }
    }
}
