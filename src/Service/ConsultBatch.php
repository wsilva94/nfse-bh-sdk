<?php namespace NFse\Service;

use Exception;
use NFse\Models\Settings;
use NFse\Sanitizers\Text;
use NFse\Soap\ConsultaLoteRps;
use NFse\Soap\ErrorLote;
use NFse\Soap\ErrorMsg;
use NFse\Soap\Soap;

class ConsultBatch extends ConsultBase
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
        parent::__construct();
        $this->text = new Text();
        $this->xSoap = new Soap($settings, 'ConsultarLoteRpsRequest');

        $parameters = (object) [
            'numProtocol' => $numProtocol,
            'file' => 'consultaLoteRps',
        ];

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
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }

        //carrega o xml de resposta para um object
        $xmlResponse = simplexml_load_string($wsResponse->outputXML);

        //identifica o retorno e faz o processamento nescessário
        if (is_object($xmlResponse) && isset($xmlResponse->ListaMensagemRetornoLote)) {
            $wsError = new ErrorMsg($xmlResponse);
            $messages = $wsError->getMessages('ListaMensagemRetornoLote', true);

            return (object) $this->errors = ($messages) ? $messages : $wsError->getError();
        } else {
            $wsLote = new ConsultaLoteRps($wsResponse);
            return (object) $wsLote->getDadosLote();
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
            $wsErrorLote = new ErrorLote($xmlResponse);
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
