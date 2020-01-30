<?php

namespace NFse\Sanitizers;

class Num
{

    private $num;

    /**
     * Inicializa a variavel numérica
     */
    public function with($num)
    {
        $this->num = $num;
        return $this;
    }

    /**
     * Filtra deixando somente números
     */
    public function sanitize()
    {
        $this->num = preg_replace("/[^0-9]/", "", $this->num);
        return $this;
    }

    /**
     * Seta um length para o attr
     */
    public function maxL($max)
    {
        $this->num = substr($this->num, 0, $max);
        return $this;
    }

    /**
     *  Retorna o valor processado
     */
    public function get()
    {
        return $this->num;
    }
}
