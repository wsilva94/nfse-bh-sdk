<?php namespace NFse\Config;

use Exception;
use NFse\Models\Settings;

class WebService
{
    public $env;
    public $wsdl = null;
    public $folder = null;
    public $soapVersion = SOAP_1_1;
    public $connectionTimeout = 10;
    public $exceptions = true;
    public $trace = true;
    public $use = SOAP_LITERAL;
    public $style = SOAP_DOCUMENT;
    public $cacheWsdl = WSDL_CACHE_NONE;
    public $compression = 0;
    public $sslVerifyPeer = false;
    public $sslVerifyPeerName = false;

    /**
     * construtor
     *
     * @param NFse\Models\Settings;
     */
    public function __construct(Settings $settings)
    {
        try {
            $this->env = $settings->environment;
            if ($this->env == 'homologacao') {
                $this->homologacao();
            } else {
                $this->producao();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * configuração para o ambiente de homologacao
     */
    private function homologacao(): void
    {
        $this->wsdl = 'https://bhisshomologa.pbh.gov.br/bhiss-ws/nfse?wsdl';
        $this->folder = 'homologacao';
    }

    /**
     * configuração para o ambiente de produção
     */
    private function producao(): void
    {
        $this->wsdl = 'https://bhissdigital.pbh.gov.br/bhiss-ws/nfse?wsdl';
        $this->folder = 'producao';
    }
}
