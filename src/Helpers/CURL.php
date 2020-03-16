<?php namespace NFse\Helpers;

use Exception;

class CURL
{
    private $cURL;
    private $URL;
    private $response;
    private $error;
    private $defaultRefer = 'http://app.metropolecontabil.com.br';

    /**
     * Instancia a extensão
     */
    public function __construct()
    {
        $this->cURL = curl_init();
        $this->autoConfig();
    }

    /**
     * Autoconfiguração da extensão
     */
    private function autoConfig()
    {
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * Retorna o erro caso houver
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     *  Seta a url do endpoint
     */
    public function withURL($url)
    {
        $this->URL = $url;
        curl_setopt($this->cURL, CURLOPT_URL, $this->URL);
        return $this;
    }

    /**
     *  Seta o método de conexão
     */
    public function withPOST()
    {
        curl_setopt($this->cURL, CURLOPT_POST, true);
        return $this;
    }

    /**
     *  Seta o parâmetros a serem enviados (formato parse string)
     */
    public function withFields($fields)
    {
        curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $fields);
        return $this;
    }

    /**
     * Seta o refere no cabeçalho (nescessário em alguns sites)
     */
    public function withRefer($refer = null)
    {
        curl_setopt($this->cURL, CURLOPT_REFERER, ((!$refer) ? $this->defaultRefer : $refer));
        return $this;
    }

    /**
     * Seta os headers
     */
    public function withHeaders($headers)
    {
        curl_setopt($this->cURL, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Faz a chamada obtendo os dados da requisição
     */
    public function execute()
    {
        try {
            //chama a url
            $this->response = curl_exec($this->cURL);

            //se der erro joga no atributo
            if ($this->response === false) {
                $this->error = curl_error($this->cURL);
            }
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
        }

        //close no handler
        curl_close($this->cURL);

        //retorna o encadeamento
        return $this;
    }

    /**
     * Burla a proteção CSRF de alguns sites e frameworks PHP usando o file_get_contents
     */
    public function executeWithoutCRSF()
    {
        try {
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $this->response = file_get_contents($this->URL, false, stream_context_create($arrContextOptions));
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
        }

        return $this;
    }

    /**
     * Retorna os dados em formato string
     */
    public function getResult()
    {
        return (!empty($this->response)) ? $this->response : false;
    }

    /**
     * Retorna os dados em formato json
     */
    public function getJson()
    {
        return (!empty($this->response)) ? json_decode($this->response) : false;
    }
}
