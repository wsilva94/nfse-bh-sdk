<?php namespace NFse\Helpers;

use NFse\Config\WebService;

class Utils
{
    public static function isDate($str_dt, $str_dateformat, $str_timezone)
    {
        $date = \DateTime::createFromFormat($str_dateformat, $str_dt, new \DateTimeZone($str_timezone));
        return $date && \DateTime::getLastErrors()["warning_count"] == 0 && \DateTime::getLastErrors()["error_count"] == 0;
    }

    public static function isValor($valor)
    {
        return true;
    }

    //limpa um xml
    public static function xmlFilter($xml)
    {
        $remove = ['xmlns:default="http://www.w3.org/2000/09/xmldsig#"', ' standalone="no"', 'default:', ':default', "\n", "\r", "\t", "  "];
        $encode = ['<?xml version="1.0"?>', '<?xml version="1.0" encoding="utf-8"?>', '<?xml version="1.0" encoding="UTF-8"?>', '<?xml version="1.0" encoding="utf-8" standalone="no"?>', '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'];
        return str_replace(array_merge($remove, $encode), '', $xml);
    }

    /**
     * ativa o modo debug
     */
    public static function xdebugMode()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    //exibe somente exceptions, warnings e erros de parsing
    public static function liteDebugMode()
    {
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
    }

    /*     //helper para amazenas os xml's na pasta storage
        public static function saveAt($filename, $fileContents)
        {
            $webservice = new WebService();
            $filePath = NFE_PATH . 'storage' . DIRECTORY_SEPARATOR . $webservice->env . DIRECTORY_SEPARATOR . $filename;
            return file_put_contents($filePath, $fileContents);
        }

        //retorna um xml da pasta storage
        public function getFile($filename)
        {
            $webservice = new WebService();
            $filePath = NFE_PATH . 'storage' . DIRECTORY_SEPARATOR . $webservice->env . DIRECTORY_SEPARATOR . $filename;
            return file_get_contents($filePath);
        }
     */
    /**
     * Função que formata valor em moeda brasileira
     *
     * @param int $valor
     */
    public static function formatRealMoney(int $valor)
    {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }

    /**
     * Função que monta uma máscara de acordo com os parâmetro informados
     *
     * @param String $val valor a ser fromatado
     * @param String $mask formato da máscara
     */
    public static function mask(String $val, String $mask)
    {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= \strlen($mask) - 1; ++$i) {
            if ($mask[$i] == '#') {
                if (isset($val[$k])) {
                    $maskared .= $val[$k++];
                }
            } else {
                if (isset($mask[$i])) {
                    $maskared .= $mask[$i];
                }
            }
        }

        return $maskared;
    }

    /**
     * Função que adiciona uma máscara de telefone em um numero.
     *
     * @param int $val número do telefone
     */
    public static function addPhoneMask($val)
    {
        $val = preg_replace('/\D/', '', $val);
        if (empty($val)) {
            return '';
        }

        $mask = '(##) ####-####';
        if (\strlen($val) == 11) {
            $mask = '(##) #####-####';
        }

        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= \strlen($mask) - 1; ++$i) {
            if ($mask[$i] == '#') {
                if (isset($val[$k])) {
                    $maskared .= $val[$k++];
                }
            } else {
                if (isset($mask[$i])) {
                    $maskared .= $mask[$i];
                }
            }
        }

        return $maskared;
    }
}
