<?php namespace Nfse\Signature;

use Exception;
use Nfse\Models\Settings;
use Nfse\Signature\Pkcs12;

class Subscriber
{

    private $settings;
    private $pcks12;

    /**
     * @param Nfse\Models\Settings;
     */
    public function __construct($settings)
    {
        $this->settings = $settings;
        $this->pcks12 = new Pkcs12($this->settings);
    }

    /**
     * checka se o load do certificado ocorreu corretamente;
     */
    public function loadPFX(): bool
    {
        $state = $this->pcks12->loadPFX();

        if (!$state) {
            throw new Exception($this->pcks12->getError());
        }

        return true;
    }

    //assina uma TAG em uma string XML
    public function assina($xml, $tag)
    {

        //faz a assinatura
        $signed = $this->pcks12->signXML($xml, $tag);

        //se retornou o content em string retorna o xml assinado
        if ($signed) {
            return $signed;
        } else {
            throw new \Exception($this->pcks12->getError());
        }

    }
}
