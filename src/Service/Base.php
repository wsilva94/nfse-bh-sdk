<?php namespace Nfse\Service;

use Exception;
use Illuminate\Support\Arr;
use Nfse\Helpers\XML;
use Nfse\Provider\Settings;
use Nfse\Sanitizers\Num;
use Nfse\Sanitizers\Text;

class Base
{
    protected $num;
    protected $errors;
    protected $xml;

    public function __construct()
    {
        $this->text = new Text();
        $this->num = new Num();
    }

    protected function getXML()
    {
        return $this->xml;
    }

    protected function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param Nfse\Provider\Settings;
     * @param object
     */
    protected function loadConsultXML(Settings $settings, array $parameters)
    {
        switch (Arr::get($parameters, 'file')) {
            case 'consultaSituacaoLoteRps':
            case 'consultRPSBatch':

                $this->xml = XML::load(Arr::get($parameters, 'file'))
                    ->set('cnpjPrestador', $settings->issuer->cnpj)
                    ->set('imPrestador', $settings->issuer->imun)
                    ->set('protocoloLote', $this->text->init(Arr::get($parameters, 'numProtocol'))->sanitize()->get())
                    ->filter()->save();
                break;

            case 'consultaNFs':

                $this->xml = XML::load(Arr::get($parameters, 'file'))
                    ->set('cnpj', $settings->issuer->cnpj)
                    ->set('inscricaoMunicipal', $settings->issuer->imun)
                    ->save();
                break;

            case 'cancelamentoNFs':

                $this->xml = XML::load(Arr::get($parameters, 'file'))
                    ->set('Id', Arr::get($parameters, 'id'))
                    ->set('numeroNFSe', $this->num->init(Arr::get($parameters, 'numerNFse'))->sanitize()->get())
                    ->set('cnpjPrestador', $settings->issuer->cnpj)
                    ->set('imPrestador', $settings->issuer->imun)
                    ->set('codigoMunicipioPrestaor', $settings->issuer->codMun)
                    ->set('codigoCancelamento', Arr::get($parameters, 'cancellationCode'))
                    ->filter()
                    ->save();
                break;

            default:
                throw new Exception("Falha ao carregar arquivo XML");
                break;
        }
    }
}
