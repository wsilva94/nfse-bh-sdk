<?php namespace NFse\Service;

use Exception;
use NFse\Helpers\XML;
use NFse\Models\Settings;
use NFse\Sanitizers\Num;
use NFse\Sanitizers\Text;

class ConsultBase
{
    protected $num;
    protected $errors;
    protected $xml;

    public function __construct()
    {
        $this->text = new Text();
        $this->num = new Num();
    }

    /**
     * constroi a chamada a consulta
     *
     * @param NFse\Models\Settings;
     * @param object
     */
    protected function callConsultation(Settings $settings, object $parameters): void
    {
        switch ($parameters->file) {

            case 'consultaSituacaoLoteRps':
            case 'consultaLoteRps':
                $this->xml = XML::load($parameters->file)
                    ->set('cnpjPrestador', $settings->issuer->cnpj)
                    ->set('imPrestador', $settings->issuer->imun)
                    ->set('protocoloLote', $this->text->with($parameters->numProtocol)->sanitize()->get())
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
                    ->set('numeroNFSe', $this->num->with($parameters->numerNFse)->sanitize()->get())
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

    /**
     * retorna o xml da consulta
     */
    protected function getXML(): string
    {
        return $this->xml;
    }

    /**
     * constroi o xml de consulta
     */
    protected function getErrors(): object
    {
        return $this->errors;
    }
}
