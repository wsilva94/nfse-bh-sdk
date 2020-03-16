<?php namespace NFse\Service;

use Exception;
use NFse\Helpers\Utils;
use NFse\Helpers\XML;
use NFse\Models\ConsultNFse as MdlConsultNFse;
use NFse\Models\Settings;
use NFse\Sanitizers\Num;
use NFse\Service\ConsultBase;
use NFse\Soap\ConsultaNFs;
use NFse\Soap\ErrorMsg;
use NFse\Soap\Soap;

class ConsultNFSe extends ConsultBase
{
    private $xSoap;

    const CNPJ = 1;
    const CPF = 2;

    /**
     * constroi o xml de consulta
     *
     * @param NFse\Models\Settings;
     */
    public function __construct(Settings $settings)
    {
        $this->xSoap = new Soap($settings, 'ConsultarNfseRequest');
        $this->num = new Num;

        parent::__construct();
        $this->syncModel = $this->callConsultation($settings, (object) [ 'file' => 'consultaNFs']);
    }

    /**
     * envia a consulta para o servidor da PBH
     *
     * @param NFse\Models\ConsultNFse as MdlConsultNFse;
     */
    public function sendConsultation(MdlConsultNFse $consultNFse): object
    {
        //monta o xml de pesquisa
        try {
            $this->setSearch($consultNFse);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }

        //envia a chamada para o SOAP
        try {
            $this->xSoap->setXML($this->getXMLFilter());
            $wsResponse = $this->xSoap->__soapCall();
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }

        //carrega o xml de resposta para um object
        $xmlResponse = simplexml_load_string($wsResponse->outputXML);
        //identifica o retorno e faz o processamento nescessário
        if (is_object($xmlResponse) && isset($xmlResponse->ListaMensagemRetorno)) {
            $wsError = new ErrorMsg($xmlResponse);
            $messages = $wsError->getMessages();

            return (object) $this->errors = ($messages) ? $messages : $wsError->getError();
        } else {
            $wsLote = new ConsultaNFs($wsResponse);
            return (object) $wsLote->getDadosNFs();
        }
    }

    /**
     * retorna o xml da consulta
     */
    public function getXMLFilter(): string
    {
        return Utils::xmlFilter($this->xml);
    }

    /**
     * anexa as datas de inicio e fim a consulta
     *
     * @param NFse\Models\ConsultNFse as MdlConsultNFse;
     */
    protected function setSearch(MdlConsultNFse $consultNFse): void
    {
        if (empty($consultNFse->startDate) || !Utils::isDate($consultNFse->startDate, 'Y-m-d', 'America/Sao_Paulo')) {
            throw new \Exception("A data inicial informada na pesquisa não é válida.");
        }

        // datetime posteriormente será convertido para o padrão do lote
        if (empty($consultNFse->endDate) || !Utils::isDate($consultNFse->endDate, 'Y-m-d', 'America/Sao_Paulo')) {
            throw new \Exception("A data final informada na pesquisa não é válida");
        }

        //ordena as datas da consulta
        if (strtotime($consultNFse->startDate) < strtotime($consultNFse->endDate)) {
            $initDate = $consultNFse->startDate;
            $endDate = $consultNFse->endDate;
        } else {
            $initDate = $consultNFse->endDate;
            $endDate = $consultNFse->startDate;
        }

        //ajusta a tag de identificação do tomador conforme o tipo
        $tagTaker = ($consultNFse->takerType == self::CNPJ) ? "<Cnpj>{$this->num->with($consultNFse->document)->sanitize()->get()}</Cnpj>" : "<Cpf>{$this->num->with($consultNFse->document)->sanitize()->get()}</Cpf>";

        //faz o append ao xml
        $this->xml = XML::load($this->xml, true)
            ->set('dataInicial', $initDate)
            ->set('dataFinal', $endDate)
            ->set('docTomador', $tagTaker)
            ->save();
    }
}
