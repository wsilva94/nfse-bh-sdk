<?php

/** Classe auxiliar com funções de DOM extendidas **/

namespace NFse\Signature;

use \DOMDocument;

class Dom extends DOMDocument {

    public function __construct($version = '1.0', $charset = 'utf-8'){
        parent::__construct($version, $charset);
        $this->formatOutput = false;
        $this->preserveWhiteSpace = false;
    }

    public function loadXMLString($xmlString = ''){
        if (!$this->loadXML($xmlString, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {
            $msg = "O arquivo indicado não é um XML!";
            throw new \Exception($msg);
        }
    }

    public function loadXMLFile($pathXmlFile = ''){
        $data = file_get_contents($pathXmlFile);
        $this->loadXMLString($data);
    }

    //extrai o valor do node DOM
    public function getNodeValue($nodeName, $itemNum = 0, $extraTextBefore = '', $extraTextAfter = ''){
        $node = $this->getElementsByTagName($nodeName)->item($itemNum);
        if (isset($node)) {
            $texto = html_entity_decode(trim($node->nodeValue), ENT_QUOTES, 'UTF-8');
            return $extraTextBefore . $texto . $extraTextAfter;
        }
        return '';
    }

    //getval node
    public function getValue($node, $name) {
        if (empty($node)) {
            return '';
        }
        $texto = ! empty($node->getElementsByTagName($name)->item(0)->nodeValue) ?
            $node->getElementsByTagName($name)->item(0)->nodeValue : '';
        return html_entity_decode($texto, ENT_QUOTES, 'UTF-8');
    }

    //retorna o node solicitado
    public function getNode($nodeName, $itemNum = 0){
        $node = $this->getElementsByTagName($nodeName)->item($itemNum);
        if (isset($node)) {
            return $node;
        }
        return '';
    }

    //retorna a chabe
    public function getChave($nodeName = 'infNFe'){
        $node = $this->getElementsByTagName($nodeName)->item(0);
        if (! empty($node)) {
            $chaveId = $node->getAttribute("Id");
            $chave =  preg_replace('/[^0-9]/', '', $chaveId);
            return $chave;
        }
        return '';
    }

    //adiciona um elemento ao node xml passado como referencia
    public function addChild(&$parent, $name, $content = '', $obrigatorio = false, $descricao = "", $force = false){
        $content = trim($content);
        if ($obrigatorio && $content === '' && !$force) {
            $this->erros[] = array(
                "tag" => $name,
                "desc" => $descricao,
                "erro" => "Preenchimento Obrigatório!"
            );
        }
        if ($obrigatorio || $content !== '' || $force) {
            $content = htmlspecialchars($content, ENT_QUOTES);
            $temp = $this->createElement($name, $content);
            $parent->appendChild($temp);
        }
    }

    //faz o append de um chield a um elemento xml
    public function appChild(&$parent, $child, $msg = ''){
        if (empty($parent)) {
            throw new \Exception($msg);
        }
        if (!empty($child)) {
            $parent->appendChild($child);
        }
    }

    //adiciona a um DOMNode parent, outros elementos passados em um array de DOMElements
    public function addArrayChild(&$parent, $arr){
        $num = 0;
        if (! empty($arr) && ! empty($parent)) {
            foreach ($arr as $node) {
                $this->appChild($parent, $node, '');
                $num++;
            }
        }
        return $num;
    }
}
