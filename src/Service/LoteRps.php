<?php namespace NFse\Service;

use Exception;
use NFse\Helpers\Utils;
use NFse\Models\Settings;
use NFse\Service\XmlRps;
use NFse\Signature\Subscriber;
use NFse\Soap\EnvioLoteRps;
use NFse\Soap\ErrorMsg;
use NFse\Soap\Soap;

class LoteRps
{

    private $xSoap;
    private $loteRps;
    private $xmlLote;
    private $subscriber;

    /**
     * construtor
     *
     * @param NFse\Models\Settings;
     * @param string
     */
    public function __construct(Settings $settings, string $numLote)
    {
        $this->xSoap = new Soap($settings, 'RecepcionarLoteRpsRequest');
        $this->loteRps = new XmlRps($settings, $numLote);

        $this->subscriber = new Subscriber($settings);

        //tenta carregar os certificados
        try {
            $this->subscriber->loadPFX();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * adiciona uma RPS assinada ao lote
     * @param string
     */
    public function addRps(string $signedRps): void
    {
        $this->loteRps->addRps($signedRps);
    }

    /**
     * retorna o lote pronto para envio
     */
    public function sendLote(): object
    {
        $xmlLote = Utils::xmlFilter($this->loteRps->getLoteRps());

        //tenta assinar o lote
        try {
            $signedLote = $this->subscriber->assina($xmlLote, 'LoteRps');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $this->xmlLote = $signedLote;

        //envia o request para a PBH
        try {
            $this->xSoap->setXML($signedLote);
            $wsResponse = $this->xSoap->__soapCall();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        //carrega o xml de resposta para um object
        $xmlResponse = simplexml_load_string($wsResponse->outputXML);

        //identifica o retorno e faz o processamento nescessário
        if (is_object($xmlResponse) && isset($xmlResponse->ListaMensagemRetorno)) {
            $wsError = new ErrorMsg($xmlResponse);
            return (object) [
                'success' => false,
                'messages' => (object) $wsError->getMessages(),
            ];
        } else {
            $wsLote = new EnvioLoteRps($wsResponse);
            return (object) [
                'success' => true,
                'response' => (object) $wsLote->getDadosLote(),
            ];
        }
    }

    public function getXMLLote()
    {
        return $this->xmlLote;
    }
}
