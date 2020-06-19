<?php namespace Nfse\Service;

use Nfse\Sanitizers\Num;
use Nfse\Sanitizers\Text;
use Nfse\Helpers\XML;
use Exception;
use Service\Settings;
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
     * @param Nfse\Models\Settings;
     * @param object
     */
    protected function LoadConsultXML(Settings $settings, object $parameters)
    {
        switch ($parameters->file) {
            case 'consultaSituacaoLoteRps':
            case 'consultaLoteRps':

                $this->xml = XML::load($parameters->file)
                    ->set('cnpjPrestador', $settings->issuer->cnpj)
                    ->set('imPrestador', $settings->issuer->imun)
                    ->set('protocoloLote', $this->text->init($parameters->numProtocol)->sanitize()->get())
                    ->filter()->save();
                break;

            case 'consultaNFs':

                $this->xml = XML::load($parameters->file)
                    ->set('cnpj', $settings->issuer->cnpj)
                    ->set('inscricaoMunicipal', $settings->issuer->imun)
                    ->save();
                break;

            case 'cancelamentoNFs':

                $this->xml = XML::load($parameters->file)
                    ->set('Id', $parameters->id)
                    ->set('numeroNFSe', $this->num->init($parameters->numerNFse)->sanitize()->get())
                    ->set('cnpjPrestador', $settings->issuer->cnpj)
                    ->set('imPrestador', $settings->issuer->imun)
                    ->set('codigoMunicipioPrestaor', $settings->issuer->codMun)
                    ->set('codigoCancelamento', $parameters->cancellationCode)
                    ->filter()
                    ->save();
                break;

            default:
                throw new Exception("Falha ao carregar arquivo XML");
                break;
        }
    }
}