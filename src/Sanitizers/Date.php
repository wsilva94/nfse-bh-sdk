<?php

namespace NFse\Sanitizers;

class Date
{
    private $date;

    /**
     * Inicializa a variavel
     */
    public function with($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     *  Formata em ano/mês/dia
     */
    public function formatYmd($date)
    {
        return $this->date = date('Y-m-d', strtotime($date));
    }

    /**
     *  Formata em ano/mês/dia/hora/min/seg
     */
    public function formatYmdTHis($date)
    {
        return $this->date = str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime($date)));
    }

    /**
     *  Formata uma competência mês/ano
     */
    public function formatYm($date)
    {
        return $this->date = date('Y-m', strtotime($date));
    }

    /**
     *  Retorna o valor processado
     */
    public function get()
    {
        return $this->date;
    }
}
