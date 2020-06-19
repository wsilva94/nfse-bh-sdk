<?php namespace Nfse\Signature;

//Classe auxiliar para obter informações dos certificados digitais A1 (PKCS12)

use Nfse\Signature\Oids;

class Asn extends Base
{

    //comprimento do campo sendo usado
    protected static $len = 0;

    //Obtêm o numero de CNPJ da chave publica do Certificado (A1)
    public static function getCNPJCert($certPem)
    {
        $certDer = self::pem2Der((string) $certPem);
        $data = self::getOIDdata((string) $certDer, '2.16.76.1.3.3');
        return (string) $data[0][1][1][0][1];
    }

    /**
     * getOIDdata
     * Recupera a informação referente ao OID contido no certificado
     * Este método assume que a OID está inserida dentro de uma estrutura do
     * tipo "sequencia", como primeiro elemento da estrutura
     */
    protected static function getOIDdata($certDer, $oidNumber)
    {
        //converte onumero OTD de texto para hexadecimal
        $oidHexa = self::oidtoHex((string) $oidNumber);
        //Divide o certificado usando a OID como marcador,uma antes do OID e outra contendo o OID.
        //Normalmente o certificado será dividido em duas partes, pois em geral existe
        //apenas um OID de cada tipo no certificado, mas podem haver mais.
        $partes = explode($oidHexa, $certDer);
        $ret = array();
        //se count($partes) > 1 então o OID foi localizado no certificado
        $tot = count($partes);
        if ($tot > 1) {
            //O inicio da sequencia que nos interessa pode estar a 3 ou 2 digitos
            //antes do inicio da OID, isso depende do numero de bytes usados para
            //identificar o tamanho da sequencia
            for ($i = 1; $i < $tot; $i++) {
                //recupera da primeira parte os 4 últimos digitos na parte sem o OID
                $xcv4 = substr($partes[$i - 1], strlen($partes[$i - 1]) - 4, 4);
                //recupera da primeira parte os 3 ultimos digitos na parte sem o OID
                $xcv3 = substr($partes[$i - 1], strlen($partes[$i - 1]) - 3, 3);
                //recupera da primeira parte os 2 ultimos digitos na parte em o OID
                $xcv2 = substr($partes[$i - 1], strlen($partes[$i - 1]) - 2, 2);
                //verifica se o primeiro digito é Hex 030
                if ($xcv4[0] == chr(0x30)) {
                    //se for, então tamanho é definido por esses 4 bytes
                    $xcv = $xcv4;
                } else {
                    //se for, então tamanho é definido por esses 3 bytes
                    if ($xcv3[0] == chr(0x30)) {
                        $xcv = $xcv3;
                    } else {
                        //então tamanho é definido por esses 2 bytes
                        $xcv = $xcv2;
                    }
                }
                //reconstroi a sequencia, marca do tamanho do campo, OID e
                //a parte do certificado com o OID
                $data = $xcv . $oidHexa . $partes[$i];
                //converte para decimal, o segundo digito da sequencia
                $len = (integer) ord($data[1]);
                $bytes = 0;
                // obtem tamanho da parte de dados da oid
                self::getLength($len, $bytes, (string) $data);
                // Obtem o conjunto de bytes pertencentes a oid
                $oidData = substr($data, 2 + $bytes, $len);
                //parse dos dados da oid
                $ret[] = self::parseASN((string) $oidData);
            }
        }
        return $ret;
    }

    //Retorna a informação requerida do certificado
    protected static function parseASN($data, $contextEspecific = false)
    {
        $result = array();
        while (strlen($data) > 1) {
            $class = ord($data[0]);
            switch ($class) {
                case 0x30:
                    // Sequence
                    self::parseSequence($data, $result);
                    break;
                case 0x31:
                    self::parseSetOf($data, $result);
                    break;
                case 0x01:
                    // Boolean type
                    self::parseBooleanType($data, $result);
                    break;
                case 0x02:
                    // Integer type
                    self::parseIntegerType($data, $result);
                    break;
                case 0x03:
                    self::parseBitString($data, $result);
                    break;
                case 0x04:
                    self::parseOctetSting($data, $result, $contextEspecific);
                    break;
                case 0x0C:
                    self::parseUtf8String($data, $result, $contextEspecific);
                    break;
                case 0x05:
                    // Null type
                    $data = substr($data, 2);
                    $result[] = array('null', null);
                    break;
                case 0x06:
                    self::parseOIDtype($data, $result);
                    break;
                case 0x16:
                    self::parseIA5String($data, $result);
                    break;
                case 0x12:
                case 0x14:
                case 0x15:
                case 0x81:
                    self::parseString($data, $result);
                    break;
                case 0x80:
                    // Character string type
                    self::parseCharString($data, $result);
                    break;
                case 0x13:
                case 0x86:
                    // Printable string type
                    self::parsePrintableString($data, $result);
                    break;
                case 0x17:
                    // Time types
                    self::parseTimesType($data, $result);
                    break;
                case 0x82:
                    // X509v3 extensions?
                    self::parseExtensions($data, $result, 'extension : X509v3 extensions');
                    break;
                case 0xa0:
                    // Extensions Context Especific
                    self::parseExtensions($data, $result, 'Context Especific');
                    break;
                case 0xa3:
                    // Extensions
                    self::parseExtensions($data, $result, 'extension (0xA3)');
                    break;
                case 0xe6:
                    // Hex Extensions extension (0xE6)
                    self::parseHexExtensions($data, $result, 'extension (0xE6)');
                    break;
                case 0xa1:
                    // Hex Extensions extension (0xA1)
                    self::parseHexExtensions($data, $result, 'extension (0xA1)');
                    break;
                default:
                    // Unknown
                    $result[] = 'UNKNOWN' . $data;
                    $data = '';
                    break;
            }
        }
        if (count($result) > 1) {
            return $result;
        } else {
            return array_pop($result);
        }
    }

    //parseCommon
    protected static function parseCommon($data, &$result)
    {
        self::$len = (integer) ord($data[1]);
        $bytes = 0;
        self::getLength(self::$len, $bytes, (string) $data);
        $result = substr($data, 2 + $bytes, self::$len);
        return substr($data, 2 + $bytes + self::$len);
    }

    //parseBooleanType
    protected static function parseBooleanType(&$data, &$result)
    {
        // Boolean type
        $booleanValue = (boolean) (ord($data[2]) == 0xff);
        $dataI = substr($data, 3);
        $result[] = array(
            'boolean (1)',
            $booleanValue);
        $data = $dataI;
    }

    //parseIntegerType
    protected static function parseIntegerType(&$data, &$result)
    {
        $dataI = self::parseCommon($data, $integerData);
        if (self::$len == 16) {
            $result[] = array(
                'integer(' . self::$len . ')',
                $integerData);
        } else {
            $value = 0;
            if (self::$len <= 4) {
                // metodo funciona bem para inteiros pequenos
                for ($i = 0; $i < strlen($integerData); $i++) {
                    $value = ($value << 8) | ord($integerData[$i]);
                }
            } else {
                // metodo trabalha com inteiros arbritrários
                if (extension_loaded('bcmath')) {
                    for ($i = 0; $i < strlen($integerData); $i++) {
                        $value = bcadd(bcmul($value, 256), ord($integerData[$i]));
                    }
                } else {
                    $value = -1;
                }
            }
            $result[] = array('integer(' . self::$len . ')', $value);
        }
        $data = $dataI;
    }

    //parseHexExtensions
    protected static function parseHexExtensions(&$data, &$result, $text)
    {
        $extensionData = substr($data, 0, 1);
        $dataI = substr($data, 1);
        $result[] = array(
            $text . ' (' . self::$len . ')',
            dechex($extensionData));
        $data = $dataI;
    }

    //parseTimesType
    protected static function parseTimesType(&$data, &$result)
    {
        // Time types
        $dataI = self::parseCommon($data, $timeData);
        $result[] = array(
            'utctime (' . self::$len . ')',
            $timeData);
        $data = $dataI;
    }

    //parsePrintableString
    protected static function parsePrintableString(&$data, &$result)
    {
        // Printable string type
        $data = self::parseCommon($data, $stringData);
        $result[] = array(
            'Printable String (' . self::$len . ')',
            $stringData);
    }

    //parseCharString
    protected static function parseCharString(&$data, &$result)
    {
        // Character string type
        $data = self::parseCommon($data, $stringData);
        $result[] = array(
            'string (' . self::$len . ')',
            self::printHex((string) $stringData));
    }

    //parseExtensions
    protected static function parseExtensions(&$data, &$result, $text)
    {
        // Extensions
        $data = self::parseCommon($data, $extensionData);
        $result[] = array(
            "$text (" . self::$len . ")",
            array(self::parseASN((string) $extensionData, true)));
    }

    //parseSequence
    protected static function parseSequence(&$data, &$result)
    {
        // Sequence
        $data = self::parseCommon($data, $sequenceData);
        $values = self::parseASN((string) $sequenceData);
        if (!is_array($values) || is_string($values[0])) {
            $values = array($values);
        }
        $result[] = array(
            'sequence (' . self::$len . ')',
            $values);
    }

    //parseOIDtype
    protected static function parseOIDtype(&$data, &$result)
    {
        // Object identifier type
        $data = self::parseCommon($data, $oidData);
        // Unpack the OID
        $plain = floor(ord($oidData[0]) / 40);
        $plain .= '.' . ord($oidData[0]) % 40;
        $value = 0;
        $iCount = 1;
        while ($iCount < strlen($oidData)) {
            $value = $value << 7;
            $value = $value | (ord($oidData[$iCount]) & 0x7f);
            if (!(ord($oidData[$iCount]) & 0x80)) {
                $plain .= '.' . $value;
                $value = 0;
            }
            $iCount++;
        }
        $oidResp = Oids::getOid($plain);
        if ($oidResp) {
            $result[] = array(
                'oid(' . self::$len . '): ' . $plain,
                $oidResp);
        } else {
            $result[] = array(
                'oid(' . self::$len . '): ' . $plain,
                $plain);
        }
    }

    //parseSetOf
    protected static function parseSetOf(&$data, &$result)
    {
        $data = self::parseCommon($data, $sequenceData);
        $result[] = array(
            'set (' . self::$len . ')',
            self::parseASN((string) $sequenceData));
    }

    //parseOctetSting
    protected static function parseOctetSting(&$data, &$result, $contextEspecific)
    {
        // Octetstring type
        $data = self::parseCommon($data, $octectstringData);
        if ($contextEspecific) {
            $result[] = array(
                'octet string(' . self::$len . ')',
                $octectstringData);
        } else {
            $result[] = array(
                'octet string (' . self::$len . ')',
                self::parseASN((string) $octectstringData));
        }
    }

    //parseUtf8String
    protected static function parseUtf8String(&$data, &$result, $contextEspecific)
    {
        // UTF8 STRING
        $data = self::parseCommon($data, $octectstringData);
        if ($contextEspecific) {
            $result[] = array(
                'utf8 string(' . self::$len . ')',
                $octectstringData);
        } else {
            $result[] = array(
                'utf8 string (' . self::$len . ')',
                self::parseASN((string) $octectstringData));
        }
    }

    //parseIA5String
    protected static function parseIA5String(&$data, &$result)
    {
        // Character string type
        $data = self::parseCommon($data, $stringData);
        $result[] = array(
            'IA5 String (' . self::$len . ')',
            $stringData);
    }

    //parseString
    protected static function parseString(&$data, &$result)
    {
        // Character string type
        $data = self::parseCommon($data, $stringData);
        $result[] = array(
            'string (' . self::$len . ')',
            $stringData);
    }

    //parseBitString
    protected static function parseBitString(&$data, &$result)
    {
        // Bitstring type
        $data = self::parseCommon($data, $bitstringData);
        $result[] = array(
            'bit string (' . self::$len . ')',
            'UnsedBits:' . ord($bitstringData[0]) . ':' . ord($bitstringData[1]));
    }
}
