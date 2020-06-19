<?php namespace Nfse\Sanitizers;

class Text
{

    private $str;

    public function init($str)
    {
        $this->str = $str;
        return $this;
    }

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

    public function maxL($max)
    {
        $this->str = substr($this->str, 0, $max);
        return $this;
    }

    public function get()
    {
        return $this->str;
    }
}
