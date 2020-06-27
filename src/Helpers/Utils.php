<?php namespace Nfse\Helpers;

class Utils
{

    public static function isDate($str_dt, $str_dateformat, $str_timezone)
    {
        $date = \DateTime::createFromFormat($str_dateformat, $str_dt, new \DateTimeZone($str_timezone));
        return $date && \DateTime::getLastErrors()["warning_count"] == 0 && \DateTime::getLastErrors()["error_count"] == 0;
    }

    public static function xmlFilter($xml)
    {
        $remove = ['xmlns:default="http://www.w3.org/2000/09/xmldsig#"', ' standalone="no"', 'default:', ':default', "\n", "\r", "\t", "  "];
        $encode = ['<?xml version="1.0"?>', '<?xml version="1.0" encoding="utf-8"?>', '<?xml version="1.0" encoding="UTF-8"?>', '<?xml version="1.0" encoding="utf-8" standalone="no"?>', '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'];
        return str_replace(array_merge($remove, $encode), '', $xml);
    }

    public static function xdebugMode()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    public static function liteDebugMode()
    {
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
    }

    /**
     * @param float $valor
     */
    public static function formatRealMoney(float $valor)
    {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }

    /**
     * @param string $number
     */
    public static function removerMaskTel(string $number)
    {
        return preg_replace('/\D/', '', trim($number));
    }

    /**
     * @param string $document
     */
    public static function setMaskCpfCnpj(string $document)
    {
        if (\strlen($document) < 12) {
            return self::mask($document, '###.###.###-##');
        }

        return self::mask($document, '##.###.###/####-##');
    }

     /**
     * @param string $document
     */
    public static function removerMaskCpfCnpj(string $document)
    {
        return preg_replace('/[^0-9]/', '', trim($document));
    }

    /**
     * @param string $val valor a ser fromatado
     * @param string $mask formato da máscara
     */
    public static function mask(string $val, string $mask)
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
     * @param array $datas
     */
    public static function clearArray(array $datas)
    {
        foreach ($datas as $key => $data) {
            if (is_null($data)) {
                unset($datas[$key]);
            }
        }

        return $datas;
    }

    /**
     * @param array $data
     */
    public static function convertObjectToArray($data)
    {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = self::convertObjectToArray($value);
            }
            return $result;
        }

        return $data;
    }
}
