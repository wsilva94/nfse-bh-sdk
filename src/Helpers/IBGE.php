<?php namespace NFse\Helpers;

use NFse\Sanitizers\Num;

class IBGE
{

    private $cURL;
    private $num;
    private $error;

    /**
     * Instancia a cURL helper
     */
    public function __construct()
    {
        $this->num = new Num();
        $this->cURL = new CURL();
    }

    /**
     *  Retorna o código da cidade via tabela IBGE
     */
    public function cityCode($nameClients, $cep)
    {
        $fcep = $this->num->with($cep)->sanitize()->maxL(8)->get();
        $code = $this->cURL->withURL("https://viacep.com.br/ws/{$fcep}/json/")->executeWithoutCRSF()->getJson();
        if (!$code) {
            throw new \Exception("Não foi possivel recuperar o código do município do cliente {$nameClients}");
        } else {
            if (!empty($code->ibge)) {
                return $code->ibge;
            } else {
                throw new \Exception("O código do município do cliente {$nameClients} não foi encontrado. Por favor verifique o CEP cadastrado.");
            }
        }
    }

    /**
     *  Retorna o erro
     */
    public function getError()
    {
        return $this->error;
    }
}
