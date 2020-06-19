<?php namespace Nfse\Signature;

// Classe auxiliar para obter informações dos certificados digitais A1 (PKCS12) [Base para a classe ASN]

class Base
{

    //transforma o certificado do formato PEM para o formato DER
    protected static function pem2Der($pemData)
    {
        $begin = "CERTIFICATE-----";
        $end = "-----END";
        //extrai o conteúdo do certificado entre as marcas BEGIN e END
        $pemData1 = substr($pemData, strpos($pemData, $begin) + strlen($begin));
        $pemData2 = substr($pemData1, 0, strpos($pemData1, $end));
        //converte o resultado para binário obtendo um certificado em formato DER
        $derData = base64_decode((string) $pemData2);
        return $derData;
    }

    //Converte o numero de identificação do OID em uma representação asc, coerente com o formato do certificado
    protected static function oidtoHex($oid)
    {
        if ($oid == '') {
            return '';
        }
        $abBinary = array();
        //coloca cada parte do numero do OID em uma linha da matriz
        $partes = explode('.', $oid);
        $bun = 0;
        //para cada numero compor o valor asc do mesmo
        for ($num = 0; $num < count($partes); $num++) {
            if ($num == 0) {
                $bun = 40 * $partes[$num];
            } elseif ($num == 1) {
                $bun += $partes[$num];
                $abBinary[] = $bun;
            } else {
                $abBinary = self::xBase128((array) $abBinary, (integer) $partes[$num], true);
            }
        }
        $value = chr(0x06) . chr(count($abBinary));
        //para cada item da matriz compor a string de retorno como caracter
        foreach ($abBinary as $item) {
            $value .= chr($item);
        }
        return $value;
    }

    // xBase128 Retorna o dado convertido em asc
    protected static function xBase128($abIn, $qIn, $flag)
    {
        $abc = $abIn;
        if ($qIn > 127) {
            $abc = self::xBase128($abc, floor($qIn / 128), false);
        }
        $qIn2 = $qIn % 128;
        if ($flag) {
            $abc[] = $qIn2;
        } else {
            $abc[] = 0x80 | $qIn2;
        }
        return $abc;
    }

    //retorna o valor em caracteres hexadecimais
    protected static function printHex($value)
    {
        $tabVal = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
        $hex = '';
        for ($i = 0; $i < strlen($value); $i++) {
            $lsig = ord(substr($value, $i, 1)) % 16;
            $msig = (ord(substr($value, $i, 1)) - $lsig) / 16;
            $lessSig = $tabVal[$lsig];
            $moreSig = $tabVal[$msig];
            $hex .= $moreSig . $lessSig;
        }
        return $hex;
    }

    //obtêm o comprimento do conteúdo de uma sequência de dados do certificado
    protected static function getLength(&$len, &$bytes, $data)
    {
        $len = ord($data[1]);
        $bytes = 0;
        // Testa se tamanho menor/igual a 127 bytes,
        // se for, então $len já é o tamanho do conteúdo
        if ($len & 0x80) {
            // Testa se tamanho indefinido (nao deve ocorrer em uma codificação DER)
            if ($len == chr(0x80)) {
                // Tamanho indefinido, limitado por 0x0000h
                $len = strpos($data, chr(0x00) . chr(0x00));
                $bytes = 0;
            } else {
                //é tamanho definido. diz quantos bytes formam o tamanho
                $bytes = $len & 0x0f;
                $len = 0;
                for ($i = 0; $i < $bytes; $i++) {
                    $len = ($len << 8) | ord($data[$i + 2]);
                }
            }
        }
    }
}
