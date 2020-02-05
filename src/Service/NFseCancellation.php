<?php namespace NFse\Service;

use Exception;
use NFse\Helpers\Utils;
use NFse\Models\Settings;
use NFse\Signature\Subscriber;
use NFse\Soap\CancelamentoNFs;
use NFse\Soap\ErrorMsg;
use NFse\Soap\Soap;

class NFseCancellation extends ConsultBase
{
    private $xSoap;
    private $numNFs;
    private $settings;
    private $subscriber;

    /**
     * constroi o xml de consulta
     *
     * @param NFse\Models\Settings;
     * @param object
     */
    public function __construct(Settings $settings, object $parameters)
    {
        parent::__construct();

        $this->subscriber = new Subscriber($settings);

        $this->numNFs = $parameters->numerNFse;
        $this->settings = $settings;

        $parameters->file = 'cancelamentoNFs';
        $this->xSoap = new Soap($settings, 'CancelarNfseRequest');
        $this->callConsultation($settings, $parameters);
    }

    /**
     * envia o request de cancelamento da nota
     */
    public function sendConsultation(): object
    {
        //recupera e assina o xml de cancelamento
        $xmlCancel = $this->getXML();
        try {
            $this->subscriber->loadPFX();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $sxlCancel = $this->subscriber->assina($xmlCancel, 'InfPedidoCancelamento');

        //faz um pequeno hack trocando a posição da tag de assinatura devido a um erro no parser do webservice
        $xml = new \DOMDocument('1.0', 'utf-8');
        $xml->preserveWhiteSpace = false;
        $xml->loadXML($sxlCancel);
        $signature = $xml->getElementsByTagName('Signature')[0];
        $xml->documentElement->removeChild($xml->getElementsByTagName('Signature')[0]);
        $xml->getElementsByTagName('Pedido')[0]->appendChild($signature);

        //envia a chamada para o SOAP
        try {
            $this->xSoap->setXML($xml->saveXML());
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
            $wsLote = new CancelamentoNFs($wsResponse);
            $dataCancel = $wsLote->getDataCancelamento();

            return (object) $dataCancel;
        }
    }

}
