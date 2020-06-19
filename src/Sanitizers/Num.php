<?php namespace Nfse\Sanitizers;

class Num
{
    private $num;

    public function init($num)
    {
        $this->num = $num;
        return $this;
    }

    public function sanitize()
    {
        $this->num = preg_replace("/[^0-9]/", "", $this->num);
        return $this;
    }

    public function maxL($max)
    {
        $this->num = substr($this->num, 0, $max);
        return $this;
    }

    public function get()
    {
        return $this->num;
    }
}
