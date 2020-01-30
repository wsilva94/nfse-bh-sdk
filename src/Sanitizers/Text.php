<?php

namespace NFse\Sanitizers;

class Text
{

    private $str;

    /**
     *  Inicializa a variavel
     */
    public function with($str)
    {
        $this->str = $str;
        return $this;
    }

    /**
     *  Filtra deixando somente números
     */
    public function sanitize()
    {
        $find = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        $repl = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';
        $rest = strip_tags(trim(strtr(utf8_decode($this->str), utf8_decode($find), $repl)));
        $this->str = utf8_encode($rest);
        return $this;
    }

    public function toUpper()
    {
        $this->str = strtoupper($this->str);
        return $this;
    }

    public function toLower()
    {
        $this->str = strtolower($this->str);
        return $this;
    }

    /**
     *  Seta um length para o attr
     */
    public function maxL($max)
    {
        $this->str = substr($this->str, 0, $max);
        return $this;
    }

    /**
     *  Retorna o valor processado
     */
    public function get()
    {
        return $this->str;
    }
}
